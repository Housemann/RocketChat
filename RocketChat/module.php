<?php

declare(strict_types=1);
	class RocketChat extends IPSModule
	{
		public function Create()
		{
			//Never delete this line!
			parent::Create();

			// Propertys 
			$this->RegisterPropertyString('webhook_url', '');

		}

		public function Destroy()
		{
			//Never delete this line!
			parent::Destroy();
		}

		public function ApplyChanges()
		{
			//Never delete this line!
			parent::ApplyChanges();
		}

		
		public function SendRocket_Msg(string $channel, string $message)
		{
			$ReturnMsg = $this->SendRocket(
				$channel, 
				$message, 
				$alias=null, 
				$avatar_url=null,
				$color=null,
				$author_name=null,
				$author_icon=null,
				$author_link=null,				
				$title=null,
				$title_link=null,
				$collapsed=null,
				$image=null,
				$fields=null				
			);
			return $ReturnMsg;
		}
		
		private function SendRocket(
				string $channel, 
				string $message, 
				string $alias=null, 
				string $avatar_url=null,
				string $color=null,
				string $author_name=null,
				string $author_icon=null,
				string $author_link=null,				
				string $title=null,
				string $title_link=null,
				string $collapsed=null,
				string $image=null,
				string $fields=null
		)
		{
			// Return Values	
			$rc = 0;
			$ReturnMsg = '';

			// Checks 
			if( $this->CheckChannel($channel)!=0 )
			{
				$rc = -1;
				$ReturnMsg = $this->translate('Channel must begin with #');
			}

			if( empty($message) ) // Hier ggf. wenn Bild vorhanden ist kann leer sein
			{
				$rc = -1;
				$ReturnMsg = 'Message is empty';
			}
			
			$attachments_true=false;
			if( !empty($author_name) || !empty($title) ) 
				$attachments_true = true;



			// Begin generate Payload
			if( $rc==0 )
			{
				// Begin Payload
				$curlData  = '{';

				// Fill Alias
				if( !empty($alias) ) 
				{
					$curlData .= '"alias":"'.$alias.'"';
					$curlData .= ',';
				} #if(!empty($alias)) 

				// Fill Avatar URL
				if( !empty($avatar_url) ) 
				{
					$curlData .= '"avatar":"'.$avatar_url.'"';
					$curlData .= ',';
				} #if(!empty($avatar)) {

				// Fill Channel
				$curlData .= '"channel":"'.$channel.'"';
				$curlData .= ',';

				// Fill Message
				$curlData .= '"text":"'.$message.'"';
				
				// Fill Attachments 
				if($attachments_true==true)
				{
					$curlData .= ',';
					$curlData .= '"attachments": [{';		
						
					// Fill Color
					if( !empty($color) )
					{
						$curlData .= '"color":"'.$color.'"';
					} #if( !empty($color) )

					// Fill Author Name
					if( !empty($author_name) )
					{
						if( !empty($color) )
							$curlData .= ',';

						$curlData .= '"author_name": "'.$author_name.'"';

						// Fill Author Icon
						if( !empty($author_icon) )
						{
							#if( !empty($author_name) )
								$curlData .= ',';             
							
							$curlData .= '"author_icon": "'.$author_icon.'"';
						} #if( !empty($author_icon) )

						// Fill Author Link
						if( !empty($author_link) )
						{
							#if( !empty($author_icon) )
								$curlData .= ',';

							$curlData .= '"author_link": "'.$author_link.'"';
						} #if( !empty($author_link) )
					} #if( !empty($author_name) )
					
					// Fill Title
					if( !empty($title) )
					{
						if( !empty($author_name) || !empty($color) )
							$curlData .= ',';
						
						$curlData .= '"title": "'.$title.'"';        
						
						// Fill Title Link
						if( !empty($title_link) )
						{
							$curlData .= ',';
							$curlData .= '"title_link": "'.$title_link.'"';
						} #if( !empty($title_link) )
						
						// Collapsed
						if( !empty($collapsed) )
						{
							$curlData .= ',';
							$curlData .= '"collapsed": '.$collapsed.'';
						} #if( !empty($collapsed) )
					
						// Fields füllen (nur wenn es Title gibt)
						if( !empty($fields) )
						{
							$curlData .= ',';
							$curlData .= '"fields": [';

							$curlData .= $this->ValuesToFields ($fields);
							
							$curlData .= ']'; #$curlData .= '"fields": ['; 
						} #if( !empty($fields) )
					} #if( !empty($title) )

					if( !empty($image) )
					{            
						if( !empty($title) || !empty($author_name) ) 
							$curlData .= ',';
						
						$curlData .= '"image_url":"'.$image.'"';
					}

					// Close Attachments
					$curlData .= '}]'; #$curlData .= '"attachments": [{';
				} #if($attachments_true==true)					

				// Payload End
				$curlData .= '}';

				// Debug CurlData
				$this->SendDebug(__FUNCTION__, $curlData, 0);
			}


			
			// Send Curl
			if( $rc==0 )
				$ReturnMsg = $this->SendCurl($curlData);
			
			// Function return
			return $ReturnMsg;
		}


		private function SendCurl(string $curlData)
		{
			$WebhookURL = $this->ReadPropertyString('webhook_url');

			$header = array("Content-Type:application/json","Accept: application/json");
			$ch = curl_init($WebhookURL);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $curlData);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$result = curl_exec($ch);                                                                     
			curl_close($ch);
		
			return $result;
		}

		private function CheckChannel(string $channel)
		{
			$rc = 0;
			if( !preg_match("^[#][a-zA-Z0-9]^", $channel) )
				$rc = -1;

			return $rc;
		}

		private function ValuesToFields (string $fields) 
		{
			// JSON aus String decodieren
			$array = json_decode($fields,true);
		
			// Var deklaration
			$curlData = '';
		
			// Array durchgehen
			for($i=0;$i<count($array);$i++) 
			{
				// Wenn mehrere Fields, dann komma getrennt
				if($i>0)
					$curlData .= ',';
		
				// Field ergänzen um werte
				$curlData .= '{';
				foreach($array[$i] as $field => $value)
				{
					if($field=="short")
					{
						$curlData .= '"short": '.$value.'';
						$curlData .= ',';
					}
		
					if($field=="title")
					{
						$curlData .= '"title": "'.$value.'"';
						$curlData .= ',';
					}
		
					if($field=="value")
						$curlData .= '"value": "'.$value.'"';
				}     
				// Field abschließen
				$curlData .= '}';
			}
		
			return $curlData;
		}		
	}