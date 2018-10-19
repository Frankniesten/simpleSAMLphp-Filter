<?php 
	
10 => array(
	            'class' => 'saml:NameIDAttribute',
	            'attribute' => 'uid',
	            'format' => '%V',
		),
        
	50 => array(
	    'class' => 'core:PHP',
	    'code' => '
	    	
	    	$uid = $attributes["uid"][0];
	    	$uid = substr($uid, 4);
	    	
			$url = "http://localhost:8080";
			$request = "/people/".$uid."?properties[givenName]&properties[familyName]&properties[additionalName]&properties[email]&properties[roles][]=roleName";
			$api_secret = "****";
			$api_passwd = "****";
		
			$url = $url.$request;
			$options = array(
				"http" => array(
		            "header" => "Content-type: application/json\r\n".
		            "Authorization: Basic " .base64_encode($api_secret.":".$api_passwd),
		            "method"  => $method,
		            "content" => json_encode($data, JSON_UNESCAPED_SLASHES, JSON_NUMERIC_CHECK)
					),
				"ssl" => array(
		        "verify_peer" => false,
		        "verify_peer_name" => false,
				)
			);
			
			$context  = stream_context_create($options);
			$result = file_get_contents($url, false, $context);
				
			$persondata = json_decode($result, true);
				
		    $roles = array();
		    $rolesMap = $persondata["roles"];
		    
		    foreach ($rolesMap as $key => $value) {
				$tmpRole =$value["roleName"];
				$tmpRole = strtolower($tmpRole);
				$roles[] = $tmpRole;
			}
		    
		    
		    $attributes["givenName"] = array($persondata["givenName"]);
		    $attributes["sn"] = array($persondata["familyName"]);
		    $attributes["displayName"] = array($persondata["familyName"].", ".$persondata["givenName"]." ".$persondata["additionalName"]);
		    $attributes["mail"] = array($persondata["email"]);
		    $attributes["memberOf"] = $roles;	    
			',
		),	
?>