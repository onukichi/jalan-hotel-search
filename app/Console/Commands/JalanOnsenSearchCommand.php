<?php
namespace App\Console\Commands;

use App\Models\HotSpring;
use App\Models\LargeArea;
use App\Models\Prefecture;
use App\Models\SmallArea;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class JalanOnsenSearchCommand extends Command
{
    const COUNT = 100; // 一回のリクエストで取得する件数 10~100

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jalan:onsen-search';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'じゃらん温泉検索APIから、全国の温泉情報を取得し保存するコマンド';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $client = new Client([
            'base_uri' => 'http://jws.jalan.net',
        ]);

        $apiKey = env('JALAN_API_KEY');

        $prefectures = Prefecture::select('id', 'name', 'code', 'region_id')->get();

        foreach ($prefectures as $prefecture) {
            \Log::info(" {$prefecture->name} の温泉を取得中..");

            $available = $this->getAvailableCount($client, $apiKey, $prefecture->code);

            $i = 1;
            do {
                if ($available === 0) {
                    continue;
                }

                $response = $client->request(
                    'GET',
                    "/APICommon/OnsenSearch/V1/?key={$apiKey}&pref={$prefecture->code}&start={$i}&count=".self::COUNT.'&xml_ptn=1'
                );

                $results = @simplexml_load_string($response->getBody());

                $resultRowCount = 1;
                foreach ($results as $result) {
                    if ($resultRowCount > 4) {
                        $largeArea = LargeArea::select('id')->where('name', $result->Area->LargeArea)->first();
                        $smallArea = SmallArea::select('id')->where('name', $result->Area->SmallArea)->first();

                        \DB::beginTransaction();
                        try {
                            $hotSpring = HotSpring::updateOrCreate(
                                [
                                    'onsen_name' => (string) $result->OnsenName,
                                ],
                                [
                                    'onsen_name'         => (string) $result->OnsenName,
                                    'onsen_address'      => (string) $result->OnsenAddress,
                                    'region_id'          => $prefecture->region_id,
                                    'prefecture_id'      => $prefecture->id,
                                    'large_area_id'      => $largeArea->id,
                                    'small_area_id'      => $smallArea->id,
                                    'nature_of_onsen'    => (string) $result->NatureOfOnsen,
                                    'onsen_area_name'    => (string) $result->OnsenAreaName,
                                    'onsen_area_caption' => (string) $result->OnsenAreaCaption,
                                ]
                            );

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

        \Log::info('------ Finish to get hot springs from jalan. ------');
        echo "\nコマンドが正常に終了しました\n";
    }

    public function getAvailableCount($client, $apiKey, $prefectureCode)
    {
        $availableResponse = $client->request(
            'GET',
            "/APICommon/OnsenSearch/V1/?key={$apiKey}&pref={$prefectureCode}&xml_ptn=1"
        );
        $availableResult = @simplexml_load_string($availableResponse->getBody());

        return $availableResult->NumberOfResults;
    }
}
