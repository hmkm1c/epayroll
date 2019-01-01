
<?php

$con = mysqli_connect("localhost","root","","eyesoft2_epayroll");
$json="";
// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
  else
  {
	  echo "Connection Successful";
  }
 
//if post request
if($_SERVER['REQUEST_METHOD']=='POST')
{
    //get data from POST
    $data = file_get_contents('php://input');
	
		$json=(json_decode(file_get_contents('php://input')));
		var_dump($json);
		if($json->u_user==="admin@admin.com" && $json->u_password==="admin123")
		{
			
			$sql = "INSERT INTO punch_time (member_id,branch_id,date,punch_time,club_id,type,status)
					VALUES ('".$json->u_member_id."','".$json->u_branch_id."','".$json->u_punch_date."','".$json->u_punch_time."','".$json->u_club_id."','".
					$json->u_type."','".$json->u_status."')";
		
			if ($con->query($sql) === TRUE)
			{
					echo "Punch time record added successfully";
			}
			else
			 {
				echo "Error: ";// . $sql .<br>. $con->error;
			 }
			 
			$sql = "INSERT INTO staff_attandance (staff_id,date,status,club_id,time)
					VALUES ('".$json->u_member_id."','".$json->u_punch_date."','".$json->u_status."','".$json->u_club_id."','".$json->u_punch_time."')";
		
			if ($con->query($sql) === TRUE)
			{
					echo "Staff Attendance record added successfully";
			}
			else
			 {
				echo "Error: ";// . $sql.<br> . $con->error;
			 }
			 
		}
		else
		{
			echo "InValid User";
		}
		
}
else
{
	process();
}

function process()
{
	if(staff_member_found()==true)
	{
		echo "Staff Member Found";
		staff_attendance();
		/*
				reader_code = m_info.IndRegID.ToString();
                config_mgr.writeTofile("Staff Member Found");
                found_index = latest_record_index;
                staff_attendance(m_info, this.my_form.app_attendance_tables[0, 0]);
                attendance_ebiz(m_info, this.my_form.app_attendance_tables[0, 0]);
                add_record_in_sms_history(enrollment_id, machine_ip, "staff", this.my_form.app_attendance_tables[0, 1]);
		*/
		
	}
	else if(staff_member_found()==false)
	{
		echo "Staff Member Not Found";
	}
	
}



function staff_member_found()
{
	$found_flag=false;
	$con = mysqli_connect("localhost","root","","eyesoft2_epayroll");
	
	$reader_code=4;
	$machine_ip = "192.168.1.201";
	$app_name="emultibiz";
	$search_query = "SELECT id AS MemberID, branch_id from staff  where `reader_code` = ".$reader_code." AND  `machine_ip`=\"".$machine_ip."\"";
	
	//$search_query = "SELECT id AS MemberID, branch_id from staff  where machine_ip=".$machine_ip." AND machine_ip=".$machine_ip;
	$result = $con->query($search_query);
	
	echo "<br>";
	
	if ($result!=NULL)
	{
		// output data of each row
		while($row = $result->fetch_assoc())
			{
				echo "id: " . $row["MemberID"]. " - BRANCH ID: " . $row["branch_id"]. "<br>";
			}
			$found_flag=true;
	}
	else
	 {
		 $found_flag=false;
		echo "0 results";
	}
	return $found_flag;	
}



function staff_attendance()
{
	$json=(json_decode(file_get_contents('C:\Users\khurr\Desktop\data.txt')));
	$con = mysqli_connect("localhost","root","","eyesoft2_emultibiz");		
	$present =false;
	$member_id_match=14;//member_id;
	$app_name="emultibiz";
	$attendance_table="staff_attandance";
	
	$attendance_time = $json->punch_date." ".$json->punch_time;
	
	if($member_id_match !="")
	{
		
		// Adding record in service punchtime
			$sql = "INSERT INTO punch_time (member_id,branch_id,date,punch_time,club_id,type,status)
					VALUES ('".$json->member_id."','".$json->branch_id."','".$json->punch_date."','".$json->punch_time."','".$json->club_id."','".
					$json->type."','".$json->status."')";
		
			if ($con->query($sql) === TRUE)
			{
					echo"<br>";	
					echo "Data inserted in Service Punchtime successfully";
			}
			
			
			
			// Adding record in APplication punchtime
			$sql = "INSERT INTO punch_time (member_id,branch_id,date,punch_time,club_id,type,status)
					VALUES ('".$json->member_id."','".$json->branch_id."','".$json->punch_date."','".$json->punch_time."','".$json->club_id."','".
					$json->type."','".$json->status."')";
		
			if ($con->query($sql) === TRUE)
			{
					echo"<br>";
					echo "Data inserted in Application Punchtime successfully";
			}
			if($app_name=="emultibiz")
			{
				$updsquery_temp = "UPDATE `" + "punch_time" + "` SET `branch_id`= '".$json->branch_id + "' WHERE `member_id`=".$member_id_match + " AND `date`='".$json->punch_date + "'AND `punch_time`='".$json->punch_time + "' AND `club_id`='".$json->club_id + "' ";
			
            if ($con->query($sql) === TRUE)
			{	
					echo"<br>";
					echo "Branch Id  updated in staff punch_time table successfully Branch id = ".$json->branch_id;
			
			}        
		}
			 $searchquery = "SELECT `staff_id`,`date`,`club_id` FROM `".$attendance_table+"` WHERE `staff_id`='".$member_id_match + "' AND `date`='".$json->punch_date + "'  AND `club_id`='".$json->club_id + "'";
			 
            if ($con->query($sql) === TRUE)
			{
					echo"<br>";	
					echo "Query from attendance table successfully";
             	/*while (reader.Read())
                        {
                            $sqlmembid = "";//(reader["staff_id"].ToString());
                            $sqldate = "";//(reader["date"].ToString());
                            $sqlclubid = "";//(reader["club_id"].ToString());

                        }
						*/
			}

	}
	
	
	
}

$con->close();

?>