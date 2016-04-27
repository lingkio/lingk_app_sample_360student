<?php

class Users{

    public $response = [];

    private $_entrypoint;
    private $_apikey;
    private $_secret;
    


    public function __construct($settings)
    {
        $this->_entrypoint = $settings["entrypoint"];
        $this->_apikey = $settings["apikey"];
        $this->_secret = $settings["secret"];
    }

    public function init(){

        $data_user = $this->users();

        foreach($data_user->data as $user){
            $resp_arr = array();
            $resp_arr['guid'] =  (isset($user->studentGuid))? $user->studentGuid : '-';
            $resp_arr['id'] = (isset($user->externalId))? $user->externalId : '-';
            $resp_arr['first_name'] = (isset($user->firstName))? $user->firstName : '-';
            $resp_arr['last_name'] =   (isset($user->lastName))? $user->lastName : '-';
            $resp_arr['birthdate'] = (isset($user->birthdate))? $user->birthdate : '-';
            $resp_arr['gender'] = (isset($user->gender))? $user->gender : '-';
            
            $this->response[] = $resp_arr;
        }
            /*$this->response[]['totalCount'] = $this->totalCount();*/

        $result = json_encode($this->response);
        return $result;
    }

    protected function users(){

        $service = 'v1/@self/ps/students';
        $args = "";
        try
        {
            $app = Slim\App::class;

            $timestamp = gmdate('D, d M Y H:i:s \U\T\C', time());
            $message = "$timestamp";
            $signature = base64_encode(hash_hmac('sha1', "date: ".$message, $this->_secret, true));
            if(isset($_GET)){
                 $offset = $_GET['offset'];
                 $limit = $_GET['limit'];
                 $url = $this->_entrypoint . $service  . "?offset=$offset&limit=$limit" . http_build_query($args);   
            }else{
                $url = $this->_entrypoint . $service  . "?limit=1001" . http_build_query($args);    
            }
            
            $dateheader = 'Date: '.$message;
            $authheader = 'Authorization: Signature keyId="'.$this->_apikey.'",algorithm="hmac-sha1",signature="'.urlencode($signature).'"';


            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                $dateheader,
                $authheader
            ));
            $students = curl_exec($ch);
            curl_close($ch);

            if($students) {

                return json_decode($students);

            } else {
                throw new HttpResponseException('No records found.');
            }

        } catch(HttpResponseException $e) {
            $app->response()->setStatus(404);
            return '{"error":{"text":'. $e->getMessage() .'}}';
        }


    }
    
    protected function totalCount(){
        
        
        $service = 'v1/@self/ps/students';
        $args = "";
        try
        {
            $app = Slim\App::class;

            $timestamp = gmdate('D, d M Y H:i:s \U\T\C', time());
            $message = "$timestamp";
            $signature = base64_encode(hash_hmac('sha1', "date: ".$message, $this->_secret, true));

            $url = $this->_entrypoint . $service  . "?limit=1000000" . http_build_query($args);
            $dateheader = 'Date: '.$message;
            $authheader = 'Authorization: Signature keyId="'.$this->_apikey.'",algorithm="hmac-sha1",signature="'.urlencode($signature).'"';


            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                $dateheader,
                $authheader
            ));
            $students = curl_exec($ch);
            curl_close($ch);

            if($students) {
                 $st_arr = json_decode($students);
                return count($st_arr->data);

            } else {
                throw new HttpResponseException('No records found.');
            }

        } catch(HttpResponseException $e) {
            $app->response()->setStatus(404);
            return '{"error":{"text":'. $e->getMessage() .'}}';
        }
        
        
    }

}
  
$users = new Users($settings);
echo $users->init();

?>