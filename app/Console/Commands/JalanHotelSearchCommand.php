<?php
namespace App\Console\Commands;

use App\Models\Hotel;
use App\Models\HotelAccessInformation;
use App\Models\HotelImage;
use App\Models\HotelType;
use App\Models\HotSpring;
use App\Models\LargeArea;
use App\Models\Prefecture;
use App\Models\Room;
use App\Models\RoomImage;
use App\Models\SmallArea;
use App\Services\SlackService;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class JalanHotelSearchCommand extends Command
{
    const COUNT = 100; // 一回のリクエストで取得する件数 10~100

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jalan:hotel-search
    {--p|--prefectureCode= : 開始都道府県をコードで指定}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'じゃらん宿表示APIから、全国の宿情報を取得し保存するコマンド';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->slackService = new SlackService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $startMessage = 'Start to get hotel from jalan.';
        \Log::info('------ '.$startMessage.' ------');
        $this->slackService->send('<!channel> '.env('APP_ENV').' : INFO : '.$startMessage);

        $client = new Client([
            'base_uri' => 'http://jws.jalan.net',
        ]);

        $apiKey = env('JALAN_API_KEY', 'cyg16ee43ffa7b');

        $prefectures = Prefecture::select('id', 'name', 'code', 'region_id')->where('id', 26)->get();

        $hasPrefectureSkipped = false;
        foreach ($prefectures as $prefecture) {
            if (!$hasPrefectureSkipped && $this->option('prefectureCode') !== null && $prefecture->code !== $this->option('prefectureCode')) {
                \Log::info("skip: {$prefecture->name}");
                continue;
            }
            $hasPrefectureSkipped = true;

            \Log::info(" {$prefecture->name} を取得中..");

            $available = $this->getAvailableCount($client, $apiKey, $prefecture->code);

            $i = 1;
            do {
                if ($available === 0) {
                    continue;
                }

                $response = $client->request(
                    'GET',
                    "/APIAdvance/HotelSearch/V1/?key={$apiKey}&pref={$prefecture->code}&pict_size=5&picts=5&order=4&start={$i}&count=".self::COUNT.'&xml_ptn=3'
                );

                $results = @simplexml_load_string($response->getBody());

                $resultRowCount = 1;
                foreach ($results as $result) {
                    if ($resultRowCount > 4) {
                        if (isset($result->LastUpdate['day']) && isset($result->LastUpdate['month']) && isset($result->LastUpdate['year'])) {
                            $lastUpdate = $result->LastUpdate['year'].'-'.$result->LastUpdate['month'].'-'.$result->LastUpdate['day'];
                        }

                        $hotelType = HotelType::firstOrCreate([
                            'name' => (string) $result->HotelType,
                        ]);

                        $largeArea = LargeArea::select('id')->where('name', $result->Area->LargeArea)->first();
                        $smallArea = SmallArea::select('id')->where('name', $result->Area->SmallArea)->first();

                        $hotSpring = HotSpring::select('id')->where('onsen_name', (string) $result->OnsenName)->where('large_area_id', $largeArea->id)->first();

                        \DB::beginTransaction();
                        try {
                            $hotel = Hotel::updateOrCreate(
                                [
                                    'hotel_code' => (string) $result->HotelID,
                                ],
                                [
                                    'name'             => (string) $result->HotelName,
                                    'name_kana'        => (string) $result->HotelNameKana,
                                    'post_code'        => (string) $result->PostCode,
                                    'address'          => (string) $result->HotelAddress,
                                    'region_id'        => $prefecture->region_id,
                                    'prefecture_id'    => $prefecture->id,
                                    'large_area_id'    => $largeArea->id,
                                    'small_area_id'    => $smallArea->id,
                                    'hotel_type_id'    => $hotelType->id,
                                    'catch_copy'       => (string) $result->HotelCatchCopy,
                                    'caption'          => (string) $result->HotelCaption,
                                    'checkin_time'     => (string) $result->CheckInTime,
                                    'checkout_time'    => (string) $result->CheckOutTime,
                                    'sample_rate_from' => (string) $result->SampleRateFrom,
                                    'last_update'      => empty($lastUpdate) ? null : $lastUpdate,
                                    'onsen_name'       => (string) $result->OnsenName,
                                    'hot_spring_id'    => empty($hotSpring->id) ? null : $hotSpring->id,
                                    'number_rate'      => (string) $result->NumberOfRatings,
                                    'rate'             => empty((string) $result->Rating) ? '0.00' : (string) $result->Rating,
                                    'media_type'       => 'jalan',
                                ]
                            );

                            foreach ($result->AccessInformation as $accessInformation) {
                                HotelAccessInformation::updateOrCreate(
                                    [
                                        'hotel_id' => $hotel->id,
                                        'name'     => (string) $accessInformation['name'],
                                    ],
                                    [
                                        'description' => (string) $accessInformation,
                                    ]
                                );
                            }

                            $isImageCreated = $this->saveHotelImages($result, $hotel->id);
                            if (!$isImageCreated) {
                                throw new \Exception("Fail to create images. hotelId: {$hotel->id}");
                            }

                            $isRoomsCreated = $this->saveRooms($result, $hotel->id);
                            if (!$isRoomsCreated) {
                                throw new \Exception("Fail to create rooms. hotelId: {$hotel->id}");
                            }

                            \DB::commit();
                        } catch (\Exception $e) {
                            \DB::rollback();
                            \Log::debug($e);
                            echo "\n\n\033[0;31mERROR:Failed to save database.(retrying now)\033[0m\n";
                            sleep(5);
                        }
                    }
                    $resultRowCount++;
                }

                $i = $i + self::COUNT;
                sleep(1);
            } while ($i <= $available);
            \Log::info(" {$prefecture->name} の取得が終わりました。");
        }

        $hotSprings = HotSpring::get();
        foreach ($hotSprings as $hotSpring) {
            $hotelCount             = Hotel::where('hot_spring_id', $hotSpring->id)->count();
            $hotSpring->hotel_count = $hotelCount;
            $hotSpring->updated_at  = \Carbon\Carbon::now();
            $hotSpring->save();
        }

        $this->slackService->send(env('APP_ENV').' : INFO : Finish to get hotel from jalan');
        \Log::info('------ Finish to get hotel from jalan. ------');
        echo "\nコマンドが正常に終了しました\n";
    }

    /**
     * @param $result
     * @param $hotelId
     * @return bool
     */
    public function saveHotelImages($result, $hotelId)
    {
        try {
            $rowCount = 1;
            foreach ($result->PictureURL as $picture) {
                if ($rowCount === 1) {
                    HotelImage::updateOrCreate(
                        [
                            'hotel_id'  => $hotelId,
                            'image_url' => (string) $picture,
                        ],
                        [
                            'type' => 'main',
                        ]
                    );
                } else {
                    HotelImage::updateOrCreate(
                        [
                            'hotel_id'  => $hotelId,
                            'image_url' => (string) $picture,
                        ],
                        [
                            'type' => 'sub',
                        ]
                    );
                }

                $rowCount++;
            }

            return true;
        } catch (\Exception $e) {
            \Log::error($e);
            return false;
        }
    }

    public function saveRooms($result, $hotelId)
    {
        \DB::beginTransaction();
        try {
            foreach ($result->Plan as $plan) {
                $room = Room::updateOrCreate(
                    [
                        'hotel_id' => $hotelId,
                        'code'     => $plan->RoomCD,
                    ],
                    [
                        'name'     => $plan->RoomName,
                    ]
                );

                foreach ($plan->PlanPictureURL as $planPicutre) {
                    RoomImage::firstOrCreate(
                        [
                            'room_id' => $room->id,
                            'image_url' => (string) $planPicutre,
                        ]
                    );
                }
            }

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error($e);
            return false;
        }

        return true;
    }

    /**
     * @param $client
     * @param $apiKey
     * @param $prefectureCode
     * @return \SimpleXMLElement
     */
    public function getAvailableCount($client, $apiKey, $prefectureCode)
    {
        $availableResponse = $client->request(
            'GET',
            "/APIAdvance/HotelSearch/V1/?key={$apiKey}&pref={$prefectureCode}&pict_size=5&picts=5&order=4&xml_ptn=3"
        );
        $availableResult = @simplexml_load_string($availableResponse->getBody());

        return $availableResult->NumberOfResults;
    }
}
