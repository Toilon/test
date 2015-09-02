<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';



class Video extends REST_Controller
{
    /**
     * Поиск пользовательских видео в базе по ключу
     */
    public function index_get()
    {

        $collection = $this->mongo_db->db->selectCollection('video');
        $headers = getallheaders();
        $result=$collection->find(["user"=>$headers['X_API_KEY']]);
        foreach($result as $video)
        {
            $resp_arr[] = $video;
        }

        $this->response($resp_arr, REST_Controller::HTTP_OK);
    }

    /**
     * Создание заявки на обработку видео и создание записи в БД
     */
    public function index_post()
    {
        $headers = getallheaders();
       $this->load->helper("InputDataValidate");

        validate($_FILES, "01:00","01:33");
        $video_id = md5(microtime());
       $uploaded_file = array_shift($_FILES);
       $this->amqp->push('video', [
                "video_id"=>$video_id,
               "start_time"=>$this->input->post("start"),
               "end_time"=>$this->input->post("end"),
               "type"=>$uploaded_file['type'],
               "content"=>base64_encode(file_get_contents($uploaded_file['tmp_name']))
           ]);

        $collection = $this->mongo_db->db->selectCollection('video');
        $user_data=array('user'=>$headers['X_API_KEY']
                        ,'status'=>"scheduled", "video_id"=>$video_id);
        $collection->save($user_data);

        $this->response([
            'status' => TRUE,
            'message' => 'Request successfully received'
        ], REST_Controller::HTTP_OK);

    }
}