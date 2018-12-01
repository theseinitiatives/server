<?php

defined('BASE_URL') OR exit('No direct script access allowed');

class LoginMapper extends Mapper
{
	public function getLoginInfo($username) {
        $sql = "SELECT id,username,email,created_on,last_login,active,first_name,last_name,company,phone
            from users where username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(["username" => $username]);
        $user = $stmt->fetch();
        $res['user'] = $user;

        $sql = "SELECT *
            from user_map where user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(["user_id" => $user['id']]);
        $user_map = $stmt->fetch();

        $sql = "SELECT *
            from location where location_id = :location_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(["location_id" => $user_map['location_id']]);
        $loc = $stmt->fetch();
        $res['user_location'] = $loc;

        $locs = [];
        $locs[] = $loc;
        while($loc['parent_location']!=NULL){
            $loc = $this->getParentLocation($loc);
            $locs[] = $loc;
        }
        $locs = array_reverse($locs);

        $res['locations_tree'] = $locs;        

        return $res;
    }

    private function getParentLocation($loc){
        $sql = "SELECT *
            from location where location_id = :parent_location";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(["parent_location" => $loc['parent_location']]);
        $loc = $stmt->fetch();
        return $loc;
    }
}