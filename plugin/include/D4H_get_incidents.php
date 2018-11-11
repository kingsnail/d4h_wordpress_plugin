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
function D4H_get_incidents(){
     
   ## Get the API Key to access D4H
   $UDMD4H_options         = get_option(UDMD4H_incident_import_options);
   $D4H_API_Key            = $UDMD4H_options['API_Key'];
   $D4H_Incident_Post_Type = $UDMD4H_options['Incident_Post_Type'];
   $D4H_Post_Author_ID     = $UDMD4H_options['Post_Author_ID'];
   $D4H_Base_URL           = $UDMD4H_options['Base_URL'];

   ## Populate the metadata field id's
   $D4H_META_date          = $UDMD4H_options['META_date'];
   $D4H_META_lat           = $UDMD4H_options['META_lat'];
   $D4H_META_long          = $UDMD4H_options['META_long'];
   $D4H_META_cat           = $UDMD4H_options['META_cat'];
   $D4H_META_ref           = $UDMD4H_options['META_ref'];
   
   ## Fetch the list of incidents from D4H
   $incidents = D4H_fetch_incidents($D4H_API_Key, $D4H_Base_URL, "http://woodheadmrt.org/incidents");
   
   $r = "";
   
   ## Iterate through the list to update the Wordpress incidents list
   foreach ($incidents as $i => $incident){
       # Extract the key data items from the incident
       $r=$r."<p>Processing Incident ". $incident["id"];
       $id          = $incident["id"];
       $date        = $incident["date"];
       $enddate     = $incident["enddate"];
       $ref         = $incident["ref"];
       $ref_desc    = $incident["ref_desc"];
       $description = $incident["description"];
       $lat         = $incident["lat"];
       $lng         = $incident["lng"];
       $count_att   = $incident["count_attendance"];
    
       # Look up the type of the incident from the tag data
     
       # Calculate the derived data items;
       $start_sec   = strtotime($date);
       $end_sec     = strtotime($enddate);
       $elapsed_hrs = round(($end_sec - $start_sec)/3600,1); 
       $man_hours    = round($elapsed_hrs * $count_att, 1);
       
       # Add the effort summary to the description
       $description = $description."<p>This incident involved ".$count_att." team members, and ".$man_hours." man hours of effort. Elapsed time ".$elapsed_hrs." hours.</p>";

       ## Determine the Incident Type from the title or the body of the record.
       ## Title format is "nnnn[YYYY-nn:incident type] text"
       $incident_no   = "2029-99";
       $incident_type = "Unknown";
       $a = strpos($ref,"[");
       $trybody = false;
       if ($a) {
          $b = strpos($ref, "]", $a);
          if ($b) {
             # Found both parentheses so extract the incident type
             $incident_no_and_type = substr($ref, $a, $b-$a);
             $c = strpos($incident_no_and_type, ":");
             if ($c){
                 $incident_no   = substr($incident_no_and_type,1, $c-1);
                 $incident_type = substr($incident_no_and_type,$c + 1, strlen($incident_no_and_type) - ($c +1));
             } else {
                 $trybody = true;
             }
             $ref = substr($ref, $b+1, strlen($ref)-($b+1));
          } else {
              $trybody = true;
          }
       } else {
           $trybody = true;
       }
       
       if ($trybody){
          # The code was not found in the title, so search the description
          $r = $r." Search Descritpion for Tag.";
          $a = strpos($description,"[");
          $r = $r . "a=" .$a.". ";
          
          if ($a >= 0) {
             $b = strpos($description, "]", $a);
             if ($b) {
                # Found both parentheses so extract the incident type
                $incident_no_and_type = substr($description, $a, $b-$a);
                $c = strpos($incident_no_and_type, ":");
                if ($c){
                   $incident_no   = substr($incident_no_and_type,1, $c-1);
                   $incident_type = substr($incident_no_and_type,$c + 1, strlen($incident_no_and_type) - ($c +1));
                } 
                $description = substr($description, $b+1, strlen($description)-($b+1));
                $r = $r . "Stripped tags from description.";
             } 
          } 
       }
       
       # Check this incident to see if there is a post tagged with it's ID
       $args = array(
          'post_type'  => $D4H_Incident_Post_Type,
          'meta_query' => array(
           array(
            'key'      => 'd4h_id',
            'value'    => $id,
            ' compare' => '='
             )
          )
      );

      $query = new WP_Query($args);   
 
      if( $query->have_posts() ) {
          ## Post exists so there is already and incident record
          $query->the_post();
          $wp_id = get_the_ID();
          $r = $r." - Matched WP Incident ".$wp_id;
          
          ## We have found the Wordpress Post ID, so now we can update the existing post.
          $updated_post = array(
                   'ID'           => $wp_id,
                   'post_title'   => $ref, 
                   'post_author'  => $D4H_Post_Author_ID,
                   'post_status'  => 'publish', 
                   'post_content' => $description,
                   'post_excerpt' => '',
                   'post_date'    => date('Y-m-d H:i:s',strtotime($date)));
             
          $upd_id = wp_update_post($updated_post, true);
          if (is_wp_error($upd_id)) {
             $r = $r. "<p>Error:";
             $errors = $upd_id->get_error_messages();
             foreach ($errors as $error) {
               $r = $r . $error;
             }
          } else {
              ## The post was updated, so now update the meta information too
              ## Incident Number
              update_post_meta( $upd_id, $D4H_META_ref,        $incident_no );
              update_post_meta( $upd_id, $D4H_META_cat,      $incident_type );
              update_post_meta( $upd_id, $D4H_META_lat,  $lat );
              update_post_meta( $upd_id, $D4H_META_long, $lng );
              update_post_meta( $upd_id, $D4H_META_date, date('d/m/Y H:i',strtotime($date)) );
              $r = $r. "Updated.";
          }
             
      } else {
          ## No record of this incident
          $r = $r."Incident Not Matched so creating new record. Post type =". $D4H_Incident_Post_Type."\n";
          $new_post = array(
              'post_title'   => $ref, 
              'post_type'    => $D4H_Incident_Post_Type,
              'post_status'  => 'publish', 
              'post_author'  => $D4H_Post_Author_ID,
              'post_content' => $description,
              'post_excerpt' => '',
              'post_date'    => date('Y-m-d H:i:s',strtotime($date)));
          $new_id = wp_insert_post($new_post, true);
          if (is_wp_error($new_id)){
             $r = $r . "Post for incident " . $id . " could not be created.";
             $r = $r. "<p>Error:";
             $errors = $new_id->get_error_messages();
             foreach ($errors as $error) {
               $r = $r . $error;
             }
             # Post was not created. 
          } else {
             update_post_meta( $new_id, 'd4h_id',       $id );
             update_post_meta( $new_id, $D4H_META_ref,  $incident_no );
             update_post_meta( $new_id, $D4H_META_cat,  $incident_type );
             update_post_meta( $new_id, $D4H_META_lat,  $lat );
             update_post_meta( $new_id, $D4H_META_long, $lng );
             update_post_meta( $new_id, $D4H_META_date, date('d/m/Y H:i',strtotime($date)) );
             $r = $r . "Post ".$new_id." for incident " . $id . " was successfully created.";
          }
      }
   }
   return $r;
}

?>