<?php

class Student{

    public $response = [];
    public $resposne_time_spend = [];
    private $_entrypoint;
    private $_apikey;
    private $_secret;
    private $_student_id;


    public function __construct($guid,$settings)
    {
        $this->_resposne_time_spend = array();
        $this->_student_id = $guid;
        $this->_entrypoint = $settings["entrypoint"];
        $this->_apikey = $settings["apikey"];
        $this->_secret = $settings["secret"];
    }

    private function getAllData($array){

        $resp_program = [];
        foreach($array as $item){

            $item_program = $this->getProgramData($item->programGuid);
            $resp_program['data'][] = (object)$item_program->data[0];
        }

        return (object)$resp_program;

    }

    private function getAllDataSection($array){

        $resp_program = [];
        foreach($array as $item){

            $item_program = $this->getStudentSection($item->programGuid);
            $resp_program['data'][] = (object)$item_program->data[0];
        }

        return (object)$resp_program;

    }


    private function getCourseSection(){

        $user_section = $this->getStudentSection();
        $resp_course_section = [];

        foreach($user_section as $section){
           
            $resp_course_section_item = [];
            $item_section = $this->getStudentCourseSection($section->courseSectionGuid);
              
            foreach ($item_section->data as $item_sec) {
                
                $item_calendar_section =  $this->getStudentCalendarSessionData($item_sec->calendarSessionGuid);
                $item_courses_section = $this->getStudentCoursesSectionData($item_sec->courseGuid);
                
                $resp_course_section_item['data_calendar_session'] =$item_calendar_section->data;
                $resp_course_section_item['data_courses_section'] =$item_courses_section->data;
                
                $item_course_instructors = $this->getStudentCoursesSectionInstuctor($item_sec->courseSectionGuid);
                
                foreach($item_course_instructors->data as $instructor){

                    $item_course_instructor = $this->getStudentCoursesSectionInstuctorData($instructor->staffGuid);
                    $resp_course_section_item['data_courses_section_instructor'][] = (object)$item_course_instructor->data[0];
                }
            }
            $resp_course_section[] = $resp_course_section_item;
        }

        return (object)$resp_course_section;

    }

    public function init(){

        $data_user = $this->basicInformation();
        
        $user_addresses = $this->getAddresses();
        $user_emails = $this->getEmails();
        $user_telephones = $this->getTelephones();
        $user_section = $this->getCourseSection();
        $user_program = $this->getProgram();
        $user_enrollment = $this->getEnrollments();
        $user_learner_action = $this->getLearnerActions();
        $user_program_data = $this->getAllData($user_program);
        $data_user_test = $this->basicInformationTest();
       
        $this->testTime();
    }

    public function testTime(){
           
            $table ='<table>';
                
               foreach ($this->resposne_time_spend["data"] as  $rvalue) {
              		   
                        $table.='<tr>';
                           $table.='<td>'.$rvalue.' sec</td>';
                        $table.='</tr>';  
                         
               }
                 $table.='</table>';
                 
            echo $table;
                
    }


    protected function basicInformation(){

        $service = 'v1/@self/ps/students';
        $args = ["externalId" => $this->_student_id];
        
        try
        {
            $app = Slim\App::class;

            $timestamp = gmdate('D, d M Y H:i:s \U\T\C', time());
            $message = "$timestamp";
            $signature = base64_encode(hash_hmac('sha1', "date: ".$message, $this->_secret, true));

            if ($args != "")  {
                    $url = $this->_entrypoint . $service .'?limit=1001';
                    
                 } else {
                    $url = $this->_entrypoint . $service;
              }

            
            
            $dateheader = 'Date: '.$message;
            $authheader = 'Authorization: Signature keyId="'.$this->_apikey.'",algorithm="hmac-sha1",signature="'.urlencode($signature).'"';


            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                $dateheader,
                $authheader
            ));
            
            $student = curl_exec($ch);
            
            curl_close($ch);



            if($student) {
                
                return json_decode($student);

            } else {
                throw new HttpResponseException('No records found.');
            }

        } catch(HttpResponseException $e) {
            $app->response()->setStatus(404);
            return '{"error":{"text":'. $e->getMessage() .'}}';
        }


    }

    protected function basicInformationTest(){

        $service = 'v1/@self/ps/students';
        $args = ["externalId" => $this->_student_id];
        
        try
        {
            $app = Slim\App::class;

            $timestamp = gmdate('D, d M Y H:i:s \U\T\C', time());
            $message = "$timestamp";
            $signature = base64_encode(hash_hmac('sha1', "date: ".$message, $this->_secret, true));

            if ($args != "")  {
                    $url = $this->_entrypoint . $service .'/?filter=externalId%20eq%20'.$this->_student_id;
                 } else {
                    $url = $this->_entrypoint . $service;
              }

            
            
            $dateheader = 'Date: '.$message;
            $authheader = 'Authorization: Signature keyId="'.$this->_apikey.'",algorithm="hmac-sha1",signature="'.urlencode($signature).'"';


            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                $dateheader,
                $authheader
            ));
            
            $student = curl_exec($ch);
            
            if(!curl_errno($ch))
            {
             $info = curl_getinfo($ch);
            
             $this->resposne_time_spend['data'][] = $info['url'] .' - '. $info['total_time']; 
            }
            
            curl_close($ch);



            if($student) {

                return json_decode($student);

            } else {
                throw new HttpResponseException('No records found.');
            }

        } catch(HttpResponseException $e) {
            $app->response()->setStatus(404);
            return '{"error":{"text":'. $e->getMessage() .'}}';
        }


    }



    public function getAddresses(){

        $service = 'v1/@self/ps/students';
        $args = "";
        try
        {
            $app = Slim\App::class;

            $timestamp = gmdate('D, d M Y H:i:s \U\T\C', time());
            $message = "$timestamp";
            $signature = base64_encode(hash_hmac('sha1', "date: ".$message, $this->_secret, true));

            $url = $this->_entrypoint . $service  .'/'.$this->_student_id. "/addresses";
            $dateheader = 'Date: '.$message;
            $authheader = 'Authorization: Signature keyId="'.$this->_apikey.'",algorithm="hmac-sha1",signature="'.urlencode($signature).'"';


            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                $dateheader,
                $authheader
            ));
            $student_addresses = curl_exec($ch);
            
            if(!curl_errno($ch))
            {
             $info = curl_getinfo($ch);
            
             $this->resposne_time_spend['data'][] = $info['url'] .' - '. $info['total_time']; 
            }
            
            curl_close($ch);

            if($student_addresses) {

                return json_decode($student_addresses);

            } else {
                throw new HttpResponseException('No records found.');
            }

        } catch(HttpResponseException $e) {
            $app->response()->setStatus(404);
            return '{"error":{"text":'. $e->getMessage() .'}}';
        }


    }


    public function getEmails(){

        $service = 'v1/@self/ps/students';
        $args = "";
        try
        {
            $app = Slim\App::class;

            $timestamp = gmdate('D, d M Y H:i:s \U\T\C', time());
            $message = "$timestamp";
            $signature = base64_encode(hash_hmac('sha1', "date: ".$message, $this->_secret, true));

            $url = $this->_entrypoint . $service  .'/'.$this->_student_id. "/emails";
            $dateheader = 'Date: '.$message;
            $authheader = 'Authorization: Signature keyId="'.$this->_apikey.'",algorithm="hmac-sha1",signature="'.urlencode($signature).'"';


            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                $dateheader,
                $authheader
            ));
            $student_emails = curl_exec($ch);
            
            if(!curl_errno($ch))
            {
             $info = curl_getinfo($ch);
            
             $this->resposne_time_spend['data'][] = $info['url'] .' - '. $info['total_time']; 
            }
            
            curl_close($ch);

            if($student_emails) {

                return json_decode($student_emails);

            } else {
                throw new HttpResponseException('No records found.');
            }

        } catch(HttpResponseException $e) {
            $app->response()->setStatus(404);
            return '{"error":{"text":'. $e->getMessage() .'}}';
        }


    }


    public function getTelephones(){

        $service = 'v1/@self/ps/students';
        $args = "";
        try
        {
            $app = Slim\App::class;

            $timestamp = gmdate('D, d M Y H:i:s \U\T\C', time());
            $message = "$timestamp";
            $signature = base64_encode(hash_hmac('sha1', "date: ".$message, $this->_secret, true));

            $url = $this->_entrypoint . $service  .'/'.$this->_student_id. "/telephones";
            $dateheader = 'Date: '.$message;
            $authheader = 'Authorization: Signature keyId="'.$this->_apikey.'",algorithm="hmac-sha1",signature="'.urlencode($signature).'"';


            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                $dateheader,
                $authheader
            ));
            $student_telephones = curl_exec($ch);
            
            if(!curl_errno($ch))
            {
             $info = curl_getinfo($ch);
            
             $this->resposne_time_spend['data'][] = $info['url'] .' - '. $info['total_time']; 
            }
            
            curl_close($ch);

            if($student_telephones) {

                return json_decode($student_telephones);

            } else {
                throw new HttpResponseException('No records found.');
            }

        } catch(HttpResponseException $e) {
            $app->response()->setStatus(404);
            return '{"error":{"text":'. $e->getMessage() .'}}';
        }

    }


    public function getCourseSections(){

        $service = 'v1/@self/ps/studentsections';
        $args = "";
        try
        {
            $app = Slim\App::class;

            $timestamp = gmdate('D, d M Y H:i:s \U\T\C', time());
            $message = "$timestamp";
            $signature = base64_encode(hash_hmac('sha1', "date: ".$message, $this->_secret, true));

            $url = $this->_entrypoint . $service;

            $dateheader = 'Date: '.$message;
            $authheader = 'Authorization: Signature keyId="'.$this->_apikey.'",algorithm="hmac-sha1",signature="'.urlencode($signature).'"';


            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                $dateheader,
                $authheader
            ));
            $student_courses = curl_exec($ch);
            
            if(!curl_errno($ch))
            {
             $info = curl_getinfo($ch);
            
             $this->resposne_time_spend['data'][] = $info['url'] .' - '. $info['total_time']; 
            }
            
            curl_close($ch);

            if($student_courses) {

                $resp_course = array();
                $student_courses = json_decode($student_courses);
                foreach ($student_courses->data as  $course) {

                    if(isset($this->_student_id) && !empty($this->_student_id) && ($course->studentGuid == $this->_student_id)){
                        $resp_course[] = $course;
                    }
                }

                return $resp_course;

            } else {
                throw new HttpResponseException('No records found.');
            }

        } catch(HttpResponseException $e) {
            $app->response()->setStatus(404);
            return '{"error":{"text":'. $e->getMessage() .'}}';
        }



    }

    public function getProgram(){

        $service = 'v1/@self/ps/studentprograms';
        $args = ["studentGuid" => $this->_student_id];


        try
        {
            $app = Slim\App::class;

            $timestamp = gmdate('D, d M Y H:i:s \U\T\C', time());
            $message = "$timestamp";
            $signature = base64_encode(hash_hmac('sha1', "date: ".$message, $this->_secret, true));

            if ($args != "")  {

                $url = $this->_entrypoint . $service  . "?filter=studentGuid%20eq%20" .$this->_student_id;

            } else {
                $url = $this->_entrypoint . $service;
            }

            $dateheader = 'Date: '.$message;
            $authheader = 'Authorization: Signature keyId="'.$this->_apikey.'",algorithm="hmac-sha1",signature="'.urlencode($signature).'"';


            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                $dateheader,
                $authheader
            ));
            $student_program = curl_exec($ch);
            if(!curl_errno($ch))
            {
             $info = curl_getinfo($ch);
            
             $this->resposne_time_spend['data'][] = $info['url'] .' - '. $info['total_time']; 
            }
            
            curl_close($ch);

            if($student_program) {

                $resp_program = array();
                $student_program = json_decode($student_program);
                foreach ($student_program->data as  $program) {

                    if(isset($this->_student_id) && !empty($this->_student_id) && ($program->studentGuid == $this->_student_id)){
                        $resp_program[] = $program;
                    }
                }

                return $resp_program;

            } else {
                throw new HttpResponseException('No records found.');
            }

        } catch(HttpResponseException $e) {
            $app->response()->setStatus(404);
            return '{"error":{"text":'. $e->getMessage() .'}}';
        }



    }


    public function getEnrollments(){

        $service = 'v1/@self/ps/studentenrollments';
        $args = "";
        try
        {
            $app = Slim\App::class;

            $timestamp = gmdate('D, d M Y H:i:s \U\T\C', time());
            $message = "$timestamp";
            $signature = base64_encode(hash_hmac('sha1', "date: ".$message, $this->_secret, true));

            //$url = $this->_entrypoint . $service;
            $url = 'https://www.lingkapis.com/v1/@self/ps/studentenrollments?filter=studentGuid%20eq%203b7b5afa361744c2b66a03f066920dce';    

            $dateheader = 'Date: '.$message;
            $authheader = 'Authorization: Signature keyId="'.$this->_apikey.'",algorithm="hmac-sha1",signature="'.urlencode($signature).'"';


            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                $dateheader,
                $authheader
            ));
            $student_courses = curl_exec($ch);
            
            if(!curl_errno($ch))
            {
             $info = curl_getinfo($ch);
            
             $this->resposne_time_spend['data'][] = $info['url'] .' - '. $info['total_time']; 
            }
            
            curl_close($ch);

            if($student_courses) {

                $resp_course = array();
                $student_courses = json_decode($student_courses);
                foreach ($student_courses->data as  $course) {

                    
                        $resp_course[] = $course;
                    
                }

                return $resp_course;

            } else {
                throw new HttpResponseException('No records found.');
            }

        } catch(HttpResponseException $e) {
            $app->response()->setStatus(404);
            return '{"error":{"text":'. $e->getMessage() .'}}';
        }



    }


    public function getLearnerActions(){

        $service = 'v1/@self/ps/learneractions';
        $args = ["studentGuid" => $this->_student_id];


        try
        {
            $app = Slim\App::class;

            $timestamp = gmdate('D, d M Y H:i:s \U\T\C', time());
            $message = "$timestamp";
            $signature = base64_encode(hash_hmac('sha1', "date: ".$message, $this->_secret, true));


            if ($args != "")  {

                $url = $this->_entrypoint . $service  . "?filter=studentGuid%20eq%20" . $this->_student_id;
            } else {
                $url = $this->_entrypoint . $service;
            }


            
            $dateheader = 'Date: '.$message;
            $authheader = 'Authorization: Signature keyId="'.$this->_apikey.'",algorithm="hmac-sha1",signature="'.urlencode($signature).'"';


            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                $dateheader,
                $authheader
            ));
            $student_courses = curl_exec($ch);
            
            if(!curl_errno($ch))
            {
             $info = curl_getinfo($ch);
            
             $this->resposne_time_spend['data'][] = $info['url'] .' - '. $info['total_time']; 
            }
            
            curl_close($ch);

            if($student_courses) {

                $resp_course = array();
                $student_courses = json_decode($student_courses);
                foreach ($student_courses->data as  $course) {

                    if(isset($this->_student_id) && !empty($this->_student_id) && ($course->studentGuid == $this->_student_id)){
                        $resp_course[] = $course;
                    }
                }
                
                return $student_courses;

            } else {
                throw new HttpResponseException('No records found.');
            }

        } catch(HttpResponseException $e) {
            $app->response()->setStatus(404);
            return '{"error":{"text":'. $e->getMessage() .'}}';
        }



    }



    public function getProgramData($programGuid){

        $service = 'v1/@self/ps/programs';
        $args = ["programGuid" => $programGuid];


        try
        {
            $app = Slim\App::class;

            $timestamp = gmdate('D, d M Y H:i:s \U\T\C', time());
            $message = "$timestamp";
            $signature = base64_encode(hash_hmac('sha1', "date: ".$message, $this->_secret, true));


            if ($args != "")  {

                $url = $this->_entrypoint . $service  . "?filter=programGuid%20eq%20" . $programGuid;
            } else {
                $url = $this->_entrypoint . $service;
            }

            $dateheader = 'Date: '.$message;
            $authheader = 'Authorization: Signature keyId="'.$this->_apikey.'",algorithm="hmac-sha1",signature="'.urlencode($signature).'"';


            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                $dateheader,
                $authheader
            ));
            $student_courses = curl_exec($ch);
            
            if(!curl_errno($ch))
            {
             $info = curl_getinfo($ch);
            
             $this->resposne_time_spend['data'][] = $info['url'] .' - '. $info['total_time']; 
            }
            
            curl_close($ch);

            if($student_courses) {

                $resp_program = array();
                $student_program = json_decode($student_courses);
                
                return $student_program;

            } else {
                throw new HttpResponseException('No records found.');
            }

        } catch(HttpResponseException $e) {
            $app->response()->setStatus(404);
            return '{"error":{"text":'. $e->getMessage() .'}}';
        }

    }


    public function getStudentSection(){

        $service = 'v1/@self/ps/studentsections';
        $args = ["studentGuid" => $this->_student_id];

        try
        {
            $app = Slim\App::class;

            $timestamp = gmdate('D, d M Y H:i:s \U\T\C', time());
            $message = "$timestamp";
            $signature = base64_encode(hash_hmac('sha1', "date: ".$message, $this->_secret, true));


            if ($args != "")  {

                $url = $this->_entrypoint . $service  . "?filter=studentGuid%20eq%20" . $this->_student_id;
            } else {
                $url = $this->_entrypoint . $service;
            }

            $dateheader = 'Date: '.$message;
            $authheader = 'Authorization: Signature keyId="'.$this->_apikey.'",algorithm="hmac-sha1",signature="'.urlencode($signature).'"';


            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                $dateheader,
                $authheader
            ));
            $student_section = curl_exec($ch);
            
            if(!curl_errno($ch))
            {
             $info = curl_getinfo($ch);
            
             $this->resposne_time_spend['data'][] = $info['url'] .' - '. $info['total_time']; 
            }
            curl_close($ch);

            $resp_section = array();

            if($student_section) {
                $student_section = json_decode($student_section);
                return $student_section->data;

            } else {
                throw new HttpResponseException('No records found.');
            }

        } catch(HttpResponseException $e) {
            $app->response()->setStatus(404);
            return '{"error":{"text":'. $e->getMessage() .'}}';
        }

    }


    public function getStudentCourseSection($courseSectionGuid){

        $service = 'v1/@self/ps/coursesections';
        $args = ["courseSectionGuid" => $courseSectionGuid];

        try
        {
            $app = Slim\App::class;

            $timestamp = gmdate('D, d M Y H:i:s \U\T\C', time());
            $message = "$timestamp";
            $signature = base64_encode(hash_hmac('sha1', "date: ".$message, $this->_secret, true));


            if ($args != "")  {

                $url = $this->_entrypoint . $service  . "?filter=courseSectionGuid%20eq%20" . $courseSectionGuid;
            } else {
                $url = $this->_entrypoint . $service;
            }

            $dateheader = 'Date: '.$message;
            $authheader = 'Authorization: Signature keyId="'.$this->_apikey.'",algorithm="hmac-sha1",signature="'.urlencode($signature).'"';


            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                $dateheader,
                $authheader
            ));
            $student_section = curl_exec($ch);
            
            if(!curl_errno($ch))
            {
             $info = curl_getinfo($ch);
            
             $this->resposne_time_spend['data'][] = $info['url'] .' - '. $info['total_time']; 
            }
            
            curl_close($ch);

            if($student_section) {
                $student_section = json_decode($student_section);
                return $student_section;

            } else {
                throw new HttpResponseException('No records found.');
            }

        } catch(HttpResponseException $e) {
            $app->response()->setStatus(404);
            return '{"error":{"text":'. $e->getMessage() .'}}';
        }

    }

    public function getStudentCalendarSessionData($calendarSessionGuid){

        $service = 'v1/@self/ps/calendarsessions';
        $args = ["calendarSessionGuid" => $calendarSessionGuid];


        try
        {
            $app = Slim\App::class;

            $timestamp = gmdate('D, d M Y H:i:s \U\T\C', time());
            $message = "$timestamp";
            $signature = base64_encode(hash_hmac('sha1', "date: ".$message, $this->_secret, true));


            if ($args != "")  {

                $url = $this->_entrypoint . $service  . "?filter=calendarSessionGuid%20eq%20" . $calendarSessionGuid;
            } else {
                $url = $this->_entrypoint . $service;
            }

            $dateheader = 'Date: '.$message;
            $authheader = 'Authorization: Signature keyId="'.$this->_apikey.'",algorithm="hmac-sha1",signature="'.urlencode($signature).'"';


            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                $dateheader,
                $authheader
            ));
            $student_calendar_section = curl_exec($ch);
            
            if(!curl_errno($ch))
            {
             $info = curl_getinfo($ch);
            
             $this->resposne_time_spend['data'][] = $info['url'] .' - '. $info['total_time']; 
            }
            curl_close($ch);

            if($student_calendar_section) {
                $student_calendar_section = json_decode($student_calendar_section);
                return $student_calendar_section;

            } else {
                throw new HttpResponseException('No records found.');
            }

        } catch(HttpResponseException $e) {
            $app->response()->setStatus(404);
            return '{"error":{"text":'. $e->getMessage() .'}}';
        }

    }


    public function getStudentCoursesSectionInstuctor($courseSectionGuid){

        $service = 'v1/@self/ps/sectioninstructors';
        $args = ["courseSectionGuid" =>  $courseSectionGuid];


        try
        {
            $app = Slim\App::class;

            $timestamp = gmdate('D, d M Y H:i:s \U\T\C', time());
            $message = "$timestamp";
            $signature = base64_encode(hash_hmac('sha1', "date: ".$message, $this->_secret, true));


            if ($args != "")  {

                $url = $this->_entrypoint . $service  . "?filter=courseSectionGuid%20eq%20" . $courseSectionGuid;
            } else {
                $url = $this->_entrypoint . $service;
            }

            $dateheader = 'Date: '.$message;
            $authheader = 'Authorization: Signature keyId="'.$this->_apikey.'",algorithm="hmac-sha1",signature="'.urlencode($signature).'"';


            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                $dateheader,
                $authheader
            ));
            $student_instructor_section = curl_exec($ch);
            
            if(!curl_errno($ch))
            {
             $info = curl_getinfo($ch);
            
             $this->resposne_time_spend['data'][] = $info['url'] .' - '. $info['total_time']; 
            }
            
            curl_close($ch);

            if($student_instructor_section) {
                $student_instructor_section = json_decode($student_instructor_section);
                return $student_instructor_section;

            } else {
                throw new HttpResponseException('No records found.');
            }

        } catch(HttpResponseException $e) {
            $app->response()->setStatus(404);
            return '{"error":{"text":'. $e->getMessage() .'}}';
        }

    }

    public function getStudentCoursesSectionData($courseGuid){

        $service = 'v1/@self/ps/courses';
        $args = ["courseGuid" => $courseGuid];


        try
        {
            $app = Slim\App::class;

            $timestamp = gmdate('D, d M Y H:i:s \U\T\C', time());
            $message = "$timestamp";
            $signature = base64_encode(hash_hmac('sha1', "date: ".$message, $this->_secret, true));


            if ($args != "")  {

                $url = $this->_entrypoint . $service  . "?filter=courseGuid%20eq%20" . $courseGuid;
            } else {
                $url = $this->_entrypoint . $service;
            }

            $dateheader = 'Date: '.$message;
            $authheader = 'Authorization: Signature keyId="'.$this->_apikey.'",algorithm="hmac-sha1",signature="'.urlencode($signature).'"';


            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                $dateheader,
                $authheader
            ));
            $student_courses_section = curl_exec($ch);
            
            if(!curl_errno($ch))
            {
             $info = curl_getinfo($ch);
            
             $this->resposne_time_spend['data'][] = $info['url'] .' - '. $info['total_time']; 
            }
            
            curl_close($ch);

            if($student_courses_section) {
                $student_courses_section = json_decode($student_courses_section);
                return $student_courses_section;

            } else {
                throw new HttpResponseException('No records found.');
            }

        } catch(HttpResponseException $e) {
            $app->response()->setStatus(404);
            return '{"error":{"text":'. $e->getMessage() .'}}';
        }

    }


    public function getStudentCoursesSectionInstuctorData($staffGuid){

        $service = 'v1/@self/ps/staff';
        $args = ["staffGuid" => $staffGuid];

        try
        {
            $app = Slim\App::class;

            $timestamp = gmdate('D, d M Y H:i:s \U\T\C', time());
            $message = "$timestamp";
            $signature = base64_encode(hash_hmac('sha1', "date: ".$message, $this->_secret, true));


            if ($args != "")  {

                $url = $this->_entrypoint . $service  . "?filter=staffGuid%20eq%20" . $staffGuid;
            } else {
                $url = $this->_entrypoint . $service;
            }

            $dateheader = 'Date: '.$message;
            $authheader = 'Authorization: Signature keyId="'.$this->_apikey.'",algorithm="hmac-sha1",signature="'.urlencode($signature).'"';


            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                $dateheader,
                $authheader
            ));
            $student_calendar_section = curl_exec($ch);
            
            if(!curl_errno($ch))
            {
             $info = curl_getinfo($ch);
            
             $this->resposne_time_spend['data'][] = $info['url'] .' - '. $info['total_time']; 
            }
            
            curl_close($ch);

            if($student_calendar_section) {
                $student_calendar_section = json_decode($student_calendar_section);
                return $student_calendar_section;

            } else {
                throw new HttpResponseException('No records found.');
            }

        } catch(HttpResponseException $e) {
            $app->response()->setStatus(404);
            return '{"error":{"text":'. $e->getMessage() .'}}';
        }

    }

}
   
  $student = new Student($guid,$settings);
  $student_info = $student->init();
  echo $student_info;
  exit;
?>


   
 
   
    


  
