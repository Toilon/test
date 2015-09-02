<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;


class Worker extends CI_Controller
{


    function processvideo()
    {
        $this->load->config('amqp');



        $exchange = 'video';
        $queue = 'video_q';

        $consumer_tag = 'consumer';

        $connection = new AMQPConnection($this->config->item('host'),
            $this->config->item('port')
        ,         $channel = $this->config->item('user')
        ,         $channel = $this->config->item('pass')
        ,         "/"
    );
        $channel = $connection->channel();

        $channel->queue_declare($queue, false, true, false, false);


        $callback = function($msg){
            print_r($msg); die();

            $collection = $this->mongo_db->db->selectCollection('video');
            $result=$collection->update(["video_id"=>$msg->video_id], ["status"=>"processing"]);


            sleep(range(5,10));


            $start_date = new DateTime('2000-01-01 '.$msg->start_time);
            $since_start = $start_date->diff(new DateTime('2012-09-11 '.$msg->end_time));

            $video_len = $since_start->h.":".$since_start->i.":".$since_start->s;
            $result=$collection->update(["video_id"=>$msg->video_id], ["status"=>"done", "link"=>"https://youtube.com?dffd", "video_len"=>$video_len]);
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);

        };


        $channel->basic_qos(null, 1, null);
        $channel->basic_consume($queue, '', false, false, false, false, $callback);

        while(count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();



    }


}