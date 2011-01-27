function rconRefresh()
{
    $.getJSON(BASE_URL+'index.php/dashboard/ajaxindex/?'+Math.random(), function(data) {
        // Error?
        if(data.error == 'None')
        {
            $('#rightblock').html(data.content);
        }
        else
        {
            $('#rightblock').prepend('<div class="message error">'+data.error+'. Please try again.</div>');
        }
    });
}

function rconPlayerDetails(guid)
{
    $.getJSON(BASE_URL+'index.php/dashboard/logs/'+guid, function(data) {
        // Error?
        if(data.error == 'None')
        {
            $('#detail-ip-addresses').html(data.ip);
            $('#detail-names').html(data.names);
            $('.detail-window').show('slow');
        }
        else
        {
            $('#rightblock').prepend('<div class="message">'+data.error+'. Please try again.</div>');
        }
    });
}

function rconMessage()
{
	$('#msg-submit').attr('disabled', 'disabled');
	
	$.post(BASE_URL+'index.php/dashboard/message', { message: $('#msg-message').val(), target: $('#msg-target').val() }, function(data) {
        // Error?
        if(data.error == 'None')
        {
            $('#rightblock').prepend('<div class="message">Message successfully sent.</div>');
        }
        else
        {
            $('#rightblock').prepend('<div class="message">'+data.error+'. Please try again.</div>');
        }
        
        $('#msg-submit').attr('disabled', '');
	}, 'json');
}

function rconKick(id)
{
    $.getJSON(BASE_URL+'index.php/dashboard/kick/'+id, function(data) {
        // Error?
        if(data.error == 'None')
        {
            $('#rightblock').prepend('<div class="message">User kicked. Refresh player list.</div>');
        }
        else
        {
            $('#rightblock').prepend('<div class="message">'+data.error+'. Please try again.</div>');
        }
    });
}

function rconBan(id)
{
    $.getJSON(BASE_URL+'index.php/dashboard/ban/'+id, function(data) {
        // Error?
        if(data.error == 'None')
        {
            $('#rightblock').prepend('<div class="message">User banned. Refresh player list.</div>');
        }
        else
        {
            $('#rightblock').prepend('<div class="message">'+data.error+'. Please try again.</div>');
        }
    });
}

function rconTempBan(id)
{
    $.getJSON(BASE_URL+'index.php/dashboard/tempban/'+id, function(data) {
        // Error?
        if(data.error == 'None')
        {
            $('#rightblock').prepend('<div class="message">User banned. Refresh player list.</div>');
        }
        else
        {
            $('#rightblock').prepend('<div class="message">'+data.error+'. Please try again.</div>');
        }
    });
}

$(document).ready(function(){
	$('.leftmenu li').click(function(){
		var anchor = $(this).find('a');
		
		if(anchor && anchor != 'undefined')
		{
			var href = $(anchor).attr('href');
			
			if(href && href != 'undefined')
			{
				window.location.href = href;
			}
		}
	});
});