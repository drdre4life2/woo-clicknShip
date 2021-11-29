<?php

/**
 * @package Thefirst
 */
namespace Inc\Pages;

 class Admin
{
    public  function register(){
 
      add_action( 'admin_menu', array( $this, 'add_admin_pages') );
    }


    public function add_admin_pages()
        {
    
            add_menu_page('Thefirst Plugin', 'Thefirst', 'manage_options', 'thefirst_plugin', array($this, 'admin_index'),
    
                'dishicons-store', 110);
        }
        
        public function admin_index(){

            $request = wp_remote_get( 'https://jsonplaceholder.typicode.com/users' );

            if( is_wp_error( $request ) ) {
                return false; // Bail early
            }
            
            $body = wp_remote_retrieve_body( $request );
              
            $profiles = json_decode( $body );
    
            if( ! empty( $profiles ) ) {
               // var_dump($data);
                echo '<table style="width:100%">';
               

                foreach( $profiles as $profile ) {
                    echo '<th>Name </th>' . " ". '<th>Email</th>';
                    echo '<tr>';
                   
                        echo '<a href="">' . $profile->name . "" . $profile->email.'</a>';
                        echo '</tr>';
                }
               echo '</table>';

             

               
            }
            
            //return $data;
          //  require_once plugin_dir_path( __FILE__ ). 'templates/admin.php';
    
        }
        
}