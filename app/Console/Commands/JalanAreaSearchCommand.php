<?php
namespace App\Console\Commands;

use App\Models\LargeArea;
use App\Models\Prefecture;
use App\Models\Region;
use App\Models\SmallArea;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class JalanAreaSearchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jalan:area-search';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'じゃらんエリア検索APIから、全国のエリア情報を取得し保存するコマンド';

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

        $response = $client->request(
            'GET',
            "/APICommon/AreaSearch/V1/?key={$apiKey}"
        );

        $results = @simplexml_load_string($response->getBody());

        foreach ($results->Area->Region as $regionData) {
            $region = Region::firstOrCreate([
                'name' => $regionData['name'],
                'code' => $regionData['cd'],
            ]);

            foreach ($regionData as $prefectureData) {
                $prefecture = Prefecture::firstOrCreate([
                    'name'      => $prefectureData['name'],
                    'code'      => $prefectureData['cd'],
                    'region_id' => $region->id,
                ]);

                foreach ($prefectureData as $largeAreaData) {
                    $largeArea = LargeArea::firstOrCreate([
                        'name'          => $largeAreaData['name'],
                        'code'          => $largeAreaData['cd'],
                        'region_id'     => $region->id,
                        'prefecture_id' => $prefecture->id,
                    ]);

                    foreach ($largeAreaData as $smallAreaData) {
                        $smallArea = SmallArea::firstOrCreate([
                            'name'          => $smallAreaData['name'],
                            'code'          => $smallAreaData['cd'],
                            'region_id'     => $region->id,
                            'prefecture_id' => $prefecture->id,
                            'large_area_id' => $largeArea->id,
                        ]);
                    }
                }
            }
        }
    }
}
