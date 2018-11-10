<?php
/* Copyright 2018 Mark Pearce (email: mpearce@woodheadmrt.org)

    This file is part of the D4H-Worpress Importer Plugin.

    D4H-Worpress Importer Plugin is free software: you can redistribute it 
    and/or modify it under the terms of the GNU General Public License as 
    published by the Free Software Foundation, either version 3 of the 
    License, or (at your option) any later version.

    D4H-Worpress Importer Plugin is distributed in the hope that it will be 
    useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License along 
    with D4H-Worpress Importer Plugin.  If not, see <https://www.gnu.org/licenses/>.
*/
function D4H_fetch_incidents($apikey, $base_url, $referrer){
    
   $authstring = "Authorization: Bearer ".$apikey;
   
   // Set up the header string//
   $headers = [
      'Accept: application/json,text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
      'Accept-Language: en-US,en;q=0.5',
      'Referrer-Policy: same-origin',
      'Cache-Control: no-cache',
      'Content-Type: application/json; charset=utf-8',
      'Host: api.eu.d4h.org',
      $authstring,
      'Referer:'.$referrer,
      'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:28.0) Gecko/20100101 Firefox/28.0'
   ];

   // Get cURL resource

   $master_incident_list = array();

   $QUERY_LIMIT  = 12;
   $query_offset = 0;
   $param_published = "published=1";
   $param_limit     = "limit=$QUERY_LIMIT";
   $param_incl_arch = "include_archived=0";
   $searching = true;

   while($searching){
      $param_offset = "offset=".$query_offset;
      $url = $base_url."?".$param_published."&".$param_limit."&".$param_offset."&".$param_incl_arch;
      $curl = curl_init();
      // Set some options - we are passing in a useragent too here
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_HEADER, false);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
      curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
      curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
      curl_setopt($curl, CURLOPT_URL,$url );

      // Send the request & save response to $resp
      $resp = curl_exec($curl);
      if ($resp === false) { 
         $resp = curl_error($curl);
         echo stripslashes($resp);
         $searching = false;
      } else {
         $json = json_decode($resp, true);
         $incident_list = $json['data'];
         
         ## Stop searching if there are no records returned.
         if (sizeof($incident_list) < 1){
            $searching = false;
         }
      
         ## Add the returned records to the master list
         foreach ($incident_list as $i => $incident) {
            $id = $incident["id"];
            $master_incident_list[$id] = $incident;
         }
      }
      // Close request to clear up some resources
      curl_close($curl);
   
      ## Attempt to fetch the next set of records...
      $query_offset = $query_offset + $QUERY_LIMIT;
   }

return $master_incident_list;
}
?>