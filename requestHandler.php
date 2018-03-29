<?php
  /** Handle all the request to server **/
  $post = $_POST;
  $rh 	= new requestHandler();
  if(isset($post['req_for']) && !empty($post['req_for'])){
    switch($post['req_for']){
	  case "location_update":
	    $rh->locationUpdate($post);
	    break;
	  case "get_friends_list":
	    $rh->getFriendsList($post);
		break;
	}
  }
  
Class requestHandler {
  private $conn;
  function __construct(){
	$this->conn = new PDO("mysql:dbname=navin_contacts;host=localhost", "navin", "21594");
  }
  
  function locationUpdate($data){
	// Check if the name already exists - If exists update location else insert location
	$stmt = $this->conn->prepare("SELECT id FROM friends WHERE full_name LIKE :name AND unique_id LIKE :unique_id");
	$stmt->execute(array(':name' => $data['name'], ':unique_id' => $data['unique_id']));
	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	$result = $stmt->fetch();
	
	if(isset($result['id']) && $result['id'] > 0){
		// Update
		$stmt = $this->conn->prepare("UPDATE friends SET latitude = :latitude, longitude = :longitude, updated_at = CURRENT_TIMESTAMP	WHERE id = :row_id");
		$res = $stmt->execute(array(':latitude' => $data['latitude'], ':longitude' => $data['longitude'], ':row_id' => $result['id']));
	} else {
		// Insert
		$stmt = $this->conn->prepare("INSERT INTO friends (`full_name`, `latitude`, `longitude`, `unique_id`) VALUES (:name, :latitude, :longitude, :unique_id)");
		$res = $stmt->execute(array(':name' => $data['name'], ':latitude' => $data['latitude'], ':longitude' => $data['longitude'], ':unique_id' => $data['unique_id']));
	}
	
	print_r(json_encode($res));
  }
  
  function getFriendsList($data){
	// Get all friends list
	$stmt = $this->conn->prepare("SELECT * FROM friends WHERE unique_id != :unique_id");
	$stmt->execute(array(':unique_id' => $data['unique_id']));
	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	$result = $stmt->fetchAll();
	print_r(json_encode($result));
  }
}
?>
