<script type='text/javascript'>
document.onload = functino(){
function moGetExperimentCookie() {
    var cookies = document.cookie.split(/;\s*/);
    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i];
        var control = <?php echo $post->ID ?> ;
        if (control > 0 && cookie.indexOf("mo_experiment_" + control) != -1) {
            cookie = cookie.split('=', 2);
            return cookie[1];
        }
    }
    return null;
}
var url = window.location.href;
var params = '';
url = url.split('?');
if(!url[1]){
	params = '';
}else{
	params = '&'+url[1];
}
variation_id = moGetExperimentCookie();
var isIE = window.XDomainRequest ? true : false;
if (isIE) {
        if (variation_id != null) {
            window.location =  url[0] + '?v=' + moGetExperimentCookie()+params;
        } else {
       	 window.location = url[0] + '?t=' + new Date().getTime()+params;
        }
} else {
    xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            var newDoc = document.open("text/html", "replace");
            newDoc.write(xmlhttp.responseText);
            newDoc.close();
        }
    }
    if (variation_id != null) {
        xmlhttp.open("GET", url[0] + '?v=' + moGetExperimentCookie()+params, true);
    } else {
        xmlhttp.open("GET", url[0] + '?t=' + new Date().getTime()+params, true);
    }
    xmlhttp.send();
}
}
 </script>
