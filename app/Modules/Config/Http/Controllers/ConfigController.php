<?php
namespace App\Modules\Config\Http\Controllers;

use App\Http\Controllers\Controller;
class ConfigController extends Controller
{
    public function switchEnvironment(){
        if(config('site_vars.environment') == "testing")
        {
            $this->switchToProductionDB();
            $this->switchEnvVarToProduction();

        }
        else
        {
            $this->switchToTestingDB();
            $this->switchEnvVarToTesting();

        }
        return redirect('/home');
    }


    public function switchEnvVarToTesting(){
        config(['site_vars.environment' => 'testing']);
        $fp = fopen(base_path() .'/config/site_vars.php' , 'w');
        fwrite($fp, '<?php return ' . var_export(config('site_vars'), true) . ';');
        fclose($fp);
    }
    public function switchEnvVarToProduction(){
        config(['site_vars.environment' => 'production']);
        $fp = fopen(base_path() .'/config/site_vars.php' , 'w');
        fwrite($fp, '<?php return ' . var_export(config('site_vars'), true) . ';');
        fclose($fp);
    }

    public function switchToTestingDB(){
        $config = \Config::get('database.connections.mysql');
        $config['database'] = "Korvex_testing";
        config()->set('database.connections.mysql', $config);

        $fp = fopen(base_path() .'/config/database.php' , 'w');
        fwrite($fp, '<?php return ' . var_export(config('database'), true) . ';');
        fclose($fp);
    }
    public function switchToProductionDB(){
        $config = \Config::get('database.connections.mysql');
        $config['database'] = "Korvex";
        config()->set('database.connections.mysql', $config);

        $fp = fopen(base_path() .'/config/database.php' , 'w');
        fwrite($fp, '<?php return ' . var_export(config('database'), true) . ';');
        fclose($fp);

    }


    public function viewSystemConfig(){
        return view('sysconfig');
    }
    public function updateSystemConfig(Request $request){
//      config(['site_vars.environment' => 'testing']);
        $fp = fopen(base_path() .'/config/site_vars.php' , 'w');
        fwrite($fp, '<?php return ' . var_export(config('site_vars'), true) . ';');
        fclose($fp);
        return view('sysconfig');
    }
}
