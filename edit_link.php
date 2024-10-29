<?php
if(isset($_GET['id'])){
  $id = $_GET['id'];
}
global $wpdb;
$table_name = $wpdb->prefix . "back_link";
if(isset($_POST['blt_submit'])){
		//print_r($_POST);die();
		extract($_POST);
		//print_r(extract($_POST));
		
		global $wpdb;
		$wpdb->query("UPDATE $table_name SET refer_url = '".trim($refer_url)."', redirect_url = '".trim($redirect_url)."', status = '".trim($status)."'  where id=$id");	
		echo '<script>window.location = "?page=manage_link";</script>';
	}
$result=$wpdb->get_row( "SELECT * FROM $table_name where id='$id'" );
?>
<form action="" method="post">
         	<div class="backlink_snglfld_wrap">
         		<div class="backlink_sngl_cnt">
                    <div class="backlink_txtcnt">
                        Refer URL
                    </div>
                    <div class="backlink_fldcnt">
						<?php if(isset($result->id)){$refer_url = $result->refer_url;}?>
                        <input type="text" name="refer_url" value="<?php if(isset($refer_url)){ echo $refer_url;}?>" />
                    </div>
                </div>
               <div class="backlink_sngl_cnt">
                    <div class="backlink_txtcnt">
                        Status
                    </div>
                    <div class="backlink_fldcnt">
                    	<?php if(isset($info->id)){$status = $result->status;}?>
                        <select name="status">
							<option value="301" <?php if($status == '301'){echo 'selected="selected"';}?>>301 Redirect</option>
							<option value="404" <?php if($status == '404'){echo 'selected="selected"';}?>>404 Redirect</option>
							<option value="pageid" <?php if($status == 'pageid'){echo 'selected="selected"';}?>>Custom Page</option></select>
                    </div>
                </div>
                <div class="backlink_sngl_cnt">
                    <div class="backlink_txtcnt">
                        Redirect URL
                    </div>
                    <div class="backlink_fldcnt">
                    	<?php if(isset($result->id)){$redirect_url = $result->redirect_url;}?>
                        <input type="text" name="redirect_url" value="<?php if(isset($redirect_url)){ echo $redirect_url;}?>" />
                    </div>
                </div>
              <div class="backlink_sngl_cnt">
                	<?php if(isset($result->id)){$value = 'Update';}else{$value = 'Submit';}?>
                	<input type="submit" class="button button-primary button-large" value="<?php echo $value; ?>" />
                    <input type="hidden" name="blt_submit" value="" />
                </div>
         	</div>
         </form>