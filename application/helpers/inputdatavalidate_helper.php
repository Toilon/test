<?php

function validate($files, $start_time, $end_time){

    if(!count($files) || $start_time=="" || $end_time=="")
    {
        throw new BadRequestExeption("bad_request", 401);
    }

}