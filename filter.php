<?php 
	
	10 => array(
	            'class' => 'saml:NameIDAttribute',
	            'attribute' => 'mail',
	            'format' => '%V',
		),
        
	50 => array(
	    'class' => 'core:PHP',
	    'code' => '
	    	
	    	$userEmail = $attributes["mail"][0];
	    	
			$url = "http://localhost:8080";
			$request = "/people?email=".$userEmail."&properties[givenName]&properties[familyName]&properties[additionalName]&properties[email]&properties[roles][]=roleName";
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
			$persondata = $persondata["hydra:member"][0];
			
			$userName = $persondata["givenName"].$persondata["familyName"];
		    
		    $roles = array();
		    $rolesMap = $persondata["roles"];
		    
		    foreach ($rolesMap as $key => $value) {
				
				$roles[] = $value["@id"];
			}
		    
		    $attributes["uid"] = array($persondata["id"]);
		    $attributes["firstName"] = array($persondata["givenName"]);
		    $attributes["lastName"] = array($persondata["familyName"]);
		    $attributes["displayName"] = array($persondata["givenName"]." ".$persondata["additionalName"]." ".$persondata["familyName"]);
		    $attributes["userName"] = array(strtolower($userName));
		    $attributes["mail"] = array($persondata["email"]);
		    $attributes["memberOf"] = $roles;	    
			',
		),		
?>