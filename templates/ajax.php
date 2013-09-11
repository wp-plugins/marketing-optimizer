<script type='text/javascript'>
function moGetExperimentCookie(){
                                var cookies = document.cookie.split(/;\s*/);
                                for(var i=0;i < cookies.length;i++){
                                                var cookie = cookies[i];
                                                var control = <?php echo $post->ID ?>;
                                                if(control > 0 && cookie.indexOf("mo_experiment_"+control) != -1){
                                                                cookie = cookie.split('=',2);
                                                                return cookie[1];
                                                }
                                }
                                return null;
                }
variation_id = moGetExperimentCookie();
                xmlhttp=new XMLHttpRequest();
                xmlhttp.onreadystatechange=function() {
                                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                                     var newDoc = document.open("text/html", "replace");
                                     newDoc.write(xmlhttp.responseText);
                                     newDoc.close();               
                                }
                }
                if(variation_id  != null){
                                xmlhttp.open("GET",window.location.href+'?v='+ moGetExperimentCookie(),true);
                                                                }else{
                                xmlhttp.open("GET",window.location.href+'?t='+new Date().getTime(),true);
                }
                xmlhttp.send();   
 </script>
