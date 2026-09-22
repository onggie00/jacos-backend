<?php 
    foreach($recipient as $key => $value){
?>
<page>
<style type="text/css">
    #divid2{
        margin-top: -20px;
        margin-left: -20px;
        /* background-image: url(<?php //echo base_url() . 'uploads/bg_peserta_ft_2025.jpg'; ?>); */
        background-size: 100%;
        background-repeat: no-repeat;
        height: 90%;
        width: 110%;
        font-family: arial;
        padding-bottom:50px;
        border-top: 30px solid #007AC5;
        /* border-bottom: 30px solid #007AC5; */
    }
    p{
        margin: 2px;
        padding: 2px;
    }
</style>
    <div id="divid2">
        <table border="0" style="margin-top: 10px;margin-left: 40px;">
            <tr>
                <td style="width: 700px;text-align:left;padding-top: 5px;"><img style="width:240px;height:60px;" src="<?php echo $mail_data->mail_header_file; ?>" /></td>
            </tr>
            <tr>
                <td style="border-top: 0px solid black;" colspan="3">&nbsp;</td>
            </tr>
        </table>

        <div style="background-color: #FFFFFF;margin:0px;">
            <table border="0" style="margin-left: 40px;width: 620px;">
                <?php
                    if(!empty($mail_data->mail_number)){
                        echo "<tr>";
                        echo "<td style='padding-top: 5px;text-align:center;text-decoration: underline;width: 680px;font-weight: bold;font-size: 20px;'>P E N G U M U M A N</td>";
                        echo "</tr>";
                        echo "<tr>";
                        echo "<td style='text-align:center;font-size: 16px;'>NO : ".$mail_data->mail_number."</td>";
                        echo "</tr>";
                    }
                ?>
                <?php
                    if(!empty($mail_data->mailbox_title)){
                        $title = $mail_data->mailbox_title;
                        $title = wordwrap($title, 40, "<br>\n");
                        echo "<tr>";
                        echo "<td style='text-align:center;font-size: 14px;padding-top:15px;'>Tentang</td>";
                        echo "</tr>";
                        echo "<tr>";
                        echo "<td style='text-align:center;font-size: 14px;text-transform: uppercase;word-wrap: break-word;font-weight: bold;'>".$title."</td>";
                        echo "</tr>";
                    }
                ?>
            </table>

            <table border="0" style="margin-top: 10px;margin-left: 20px;">
                <tr>
                    <td>
                        <?php 
                            $mail_content = $mail_data->mail_content;
                            $mail_content = str_replace("[npp]", $value->npp, $mail_content);
                            $mail_content = str_replace("[nama_lengkap]", $value->nama_lengkap, $mail_content);
                            $mail_content = str_replace("[role]", $value->role, $mail_content);
                            echo $mail_content;
                        ?>
                    </td>
                    
                </tr>
            </table>

            <table border="0" style="margin-left: 60px;">
                <?php
                    if(!empty($mail_data->mail_address) && !empty($mail_data->mail_date)){
                        echo "<tr>";
                        echo "<td style='width: 680px;padding-top:50px;text-align:left;font-size: 14px;padding-left:5px;'>".$mail_data->mail_address.", ".$mail_data->mail_date."</td>";
                        echo "</tr>";
                    }
                    if(!empty($signature[0]->sign_position) && !empty($signature[0]->sign_name)){
                        echo "<tr>";
                        $total_signature = count($signature);
                        $width_signature = 680 / $total_signature;
                        foreach($signature as $key => $value){
                            echo "<td style='width: ".$width_signature."px;text-align:left;font-size: 14px;'>";
                                echo "<table border='0'>";
                                    echo "<tr>";
                                        echo "<td style='text-align:left;font-size: 14px;'>".wordwrap($value->sign_position, 40, "<br>\n")."</td>";
                                    echo "</tr>";
                                    if(!empty($value->sign_signature)){
                                        echo "<tr>";
                                            echo "<td style='text-align:left;margin-left: 10px;padding: 20px 0px;'><img style='width:180px;height:100px;' src='".FCPATH."uploads/mailbox_mail_signed/".$value->sign_signature."' /></td>";
                                        echo "</tr>";
                                    }
                                    echo "<tr>";
                                        echo "<td style='text-align:left;font-size: 14px;'>".$value->sign_name."</td>";
                                    echo "</tr>";
                                echo "</table>";
                            echo "</td>";
                        }
                        echo "</tr>";

                    }
                ?>
            </table>

        </div>
    </div>
    <page_footer style="color: #666666; text-align: left;font-size: 9px;">
        <hr style="height: 0px; background-color: #000000; border: none; margin: 0px; padding: 0px;">
        <span style="word-wrap: break-word;">Jl. Raya Hankam Kampus Labschool No. 15-20, Jatiranggon, Bekasi Kota 17432, Telepon : +62 21 84304138 ; 84304140, Fax : +62 21 84304236 E-mail : <span style="text-decoration: underline;">bps@labschoolcibubur.sch.id</span>, Home Page: www.lasbchoolcibubur.sch.id</span>
    </page_footer>
</page>
<?php
    }
?>