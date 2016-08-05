function request() {

    var search = jQuery("#search").val();

    var request = jQuery.getJSON(
        'http://leog.student.codeur.online/spamreportv2/search/' + search, function(data){ console.log(data); })


}

