/* Bitcoin Blackjack */

var client_seed;  //64 bit floating point number between 0 and 1
var server_seed_hash;

//var cs_editable = '<p style="text-align:center">Next Hand\'s Client Random Seed (R2):</p><div style="text-align:center"><input id="cs" type="text" name="cseed" size="37" maxlength="32">';
//var cs_noneditable = '<p style="text-align:center">Client Random Seed:</p><input readonly="readonly" id="cs" type="text" name="cseed" size="20">';
//var cs_noneditable = '<p style="text-align:center">Client Random Seed:';


var tPos=100;
var cardTime = 450;
var maxbet = null;
var minbet = null;
var prevHand = 0;



function genSeed() {
        var characterList = "0123456789abcdef";
        var i = 0;
        var seed = "";
	var randnum;
        do {
		seed = seed + characterList.charAt(Math.floor(Math.random()*characterList.length));
                i++;
        } while (i < 32);
        return seed;
}



function cardname(cnum)
{
	if(cnum == 52) {return "bbb";}
	var cname = "";
	var val = (cnum % 13) + 2;
	if(cnum <= 12) {cname = "d";}
	else if(cnum <= 25) {cname = "h";}
	else if(cnum <= 38) {cname = "c";}
	else if(cnum <= 51) {cname = "s";}
	if(val < 10) {cname += "0";}
	cname += val;
	return cname;
}

var deck={
	baseDeck: [
		'd02','d03','d04','d05','d06','d07','d08','d09','d10','d11','d12','d13','d14',
		'h02','h03','h04','h05','h06','h07','h08','h09','h10','h11','h12','h13','h14',
		'c02','c03','c04','c05','c06','c07','c08','c09','c10','c11','c12','c13','c14',
		's02','s03','s04','s05','s06','s07','s08','s09','s10','s11','s12','s13','s14',
		'bbb'
	]	
}

var dealer={
	cardsShown : 0	
}

var player1={
	cardsShown : 0
}

var player2={	
	cardsShown : 0
}

var player3={	
	cardsShown : 0
}


$(window).load(function(){ $.post("control", { "func":"getState","bet":0}, updateState,"json"); });
//_________________________Main Function______________________________
$(function() {
	//$.post("control", { "func":"getState","bet":1}, updateState,"json");
	imagePreload();
	$('.chip').click(function() {
	/*	var randomPos = Math.floor(Math.random()*15);
		var ranLeft= 350- $(this).position().left + randomPos;
		var ranBot = 100 + parseInt($(this).css('marginTop')) + randomPos;
		$(this)
			.find('img')
			.first()
			.stop(true, true)
			.clone()
			.appendTo($(this))
			.css('z-index', ++tPos).animate({
				left: ranLeft,
				bottom: ranBot
			}, 500);*/
		var temBet=parseInt(($(this).attr('class')).substr(6,3));
		if(temBet == 0)
		{
			$('#bet').find('span').fadeOut('fast').html(temBet).fadeIn('fast');
		}
		else
		{
			var newBet = Number($('#bet').find('span').html()) + temBet;
			var money =  Math.floor(Number($('#money').find('span').html()));
			var actualBet = newBet;
			
			if(newBet > money || newBet > maxbet)
			{
				if(money > maxbet)
				{
					actualBet = maxbet;
				}
				else
				{
					actualBet = money;
				}	
			}
			
			$('#bet').find('span').fadeOut('fast').html(actualBet).fadeIn('fast');
		}
	
		//player.bet+=temBet;
		//player.money-=temBet;
		//showMeMyMoney();
		//$('#deal').show()
	});

	$('#deal').click(function() {
		$('#deal').hide();
		$('.chip').hide();
		client_seed = $('#csinput').val();
		if(!client_seed.match(/^[0-9a-f]*$/))
		{
			alert("The client seed can only contain the characters 0-9 and a-f");
			$('#deal').show();
			$('.chip').show();
			return;
		}
		////$('#clientseed').html(cs_noneditable+'<br>'+client_seed+'</p><p style="text-align:center">Server Random Seed Hash (SHA256(R1+RX)):</p><p style="text-align:center">'+server_seed_hash+'</p>');
		$('#csinput').hide();
		$('#cstext').html(client_seed).show();
		//$('#cs').val(""+client_seed);

		//$('#stay').hide();
		//$('#hit').hide();
		//$('#double').hide();
		//$('#split').hide();
		//$('#msg').fadeOut('fast');
		//$('#p1msg').fadeOut('fast');
		//$('#p2msg').fadeOut('fast');
		//$('#p3msg').fadeOut('fast');
		$('.cardM.dealer').remove();
		$('.cardM.player1').remove();
		$('.cardM.player2').remove();
		$('.cardM.player3').remove();
		$('#msg').hide();
		$('#p1msg').hide();
		$('#p2msg').hide();
		$('#p3msg').hide();
		$('.curValue').empty();
		player1.cardsShown = 0;
		player2.cardsShown = 0;
		player3.cardsShown = 0;
		dealer.cardsShown = 0;
		//$('.chip img:not(:first-child)').remove();
		//$('.chip img:first-child').fadeIn('slow')
		tPos = 100;

		var myBet = Number($('#bet').find('span').html());
		var myMoney = Math.floor(Number($('#money').find('span').html()));
		var failmsg = null;
		if(myMoney < minbet)
		{
			failmsg = "You don't have enough money. (Minimum bet is "+minbet+".)";
		}
		else if(myBet < minbet)
		{
			failmsg = "The minimum bet is "+minbet+"."
		}
		
		if(failmsg != null)
		{
			$('#msg').slideToggle('fast').html(failmsg);
	
			setTimeout(function() {
				$('#msg').fadeOut('fast');
			}, 4500);
			$('#deal').show();
			$('.chip').show();
			////$('#clientseed').html(cs_editable+'<p style="text-align:center">Next Hand\'s Server Random Seed Hash (SHA256(R1+RX)):</p><p style="text-align:center">'+server_seed_hash+'</p>');
			$('#cpcurrenttext').html("Next Hand:");
			$('#cstext').hide();
			$('#csinput').show();
			////$('#cs').val(""+client_seed);
		}
		else
		{
			$.post("control", { "func":"deal","bet":myBet,"cseed":client_seed}, updateState,"json");
		}
	});
	
	$('#hit').click(function(){
		$('#deal').hide();
		$('#stay').hide();
		$('#hit').hide();
		$('#double').hide();
		$('#split').hide();
		$.post("control", { "func":"hit","bet":1}, updateState,"json");
	});
	
	$('#stay').click(function() {
		$('#deal').hide();
		$('#stay').hide();
		$('#hit').hide();
		$('#double').hide();
		$('#split').hide();
		$.post("control", { "func":"stay","bet":1}, updateState,"json");
	});
	
	$('#double').click(function() {
		$('#deal').hide();
		$('#stay').hide();
		$('#hit').hide();
		$('#double').hide();
		$('#split').hide();
		$.post("control", { "func":"double","bet":1}, updateState,"json");
	});
	
	$('#split').click(function() {
		$('#deal').hide();
		$('#stay').hide();
		$('#hit').hide();
		$('#double').hide();
		$('#split').hide();
		$.post("control", { "func":"split","bet":1}, updateState,"json");
	});
	
});
	
function showCards(handName, card, position, time, delayx, movelast) {
	if(time==null)
	{
		time = cardTime;
	}
	if(delayx == null)
	{
		delayx = 0;
	}
	if(movelast == null)
	{
		movelast = 0;
	}

	//var handName = hand['name'];
	var toShow = '';
	if(handName == 'dealer')
	{
		toShow = 20;
		xadjust = 0;
		yadjust = 0;
	}
	else if(handName == 'player1')
	{
		toShow = 405-((position-1)*20);
		xadjust = 0;
		yadjust = 0;
	}
	else if(handName == 'player2')
	{
		toShow = 405-((position-1)*20);
		xadjust = 240;
		yadjust = -30;
	}
	else if(handName == 'player3')
	{
		toShow = 405-((position-1)*20);
		xadjust = -200;
		yadjust = 0;
	}
	//handName == 'player1' ? toShow = 405 : toShow = 20;
	
	if(movelast == 0)
	{
		$('#gameField img')
			.eq(card)
			.clone()
			.appendTo('#gameField')
			.css('z-index', ++tPos)
			.addClass(handName)
			.delay(delayx)
			.fadeIn(0)
			.animate({
				top: toShow+yadjust,
				right: 385-(position*20)+xadjust
			},{queue:true, duration : time});
	}
	else
	{
		//$('#gameField img')
		$('.cardM.player'+movelast)
			//.find('.cardM.player1')
			.eq(-1)
			//.eq(player1.cardsShown + player2.cardsShown + player3.cardsShown + dealer.cardsShown + 52)
			.removeClass()
			.addClass('cardM')
			.addClass(handName)
			.css('z-index', ++tPos)
			.delay(delayx)
			.fadeIn(0)
			.animate({
				top: toShow+yadjust,
				right: 385-(position*20)+xadjust
			},{queue:true, duration : time});
	
	}
	//$('.curValue.'+hand['name']).html(hand['value']);
	//$('.curValue.'+handName).html(69);
}

/*
function showResult(message) {
	$('#resultMessage').slideToggle('fast').html(message);
	player.bet=0;
	showMeMyMoney();
	player.hand.length=	dealer.hand.length=0;
	setTimeout(function() {
		$('#deal, #stay, #hit, #resultMessage').fadeOut('fast');
		$('#gameField, .curValue').empty();
		imagePreload();
		$('.chip img:not(:first-child)').remove();
		$('.chip img:first-child').fadeIn('slow')
	}, 2000)
}
	
	function showMeMyMoney() {
		$('#bet').find('span').fadeOut('fast').html(player.bet).fadeIn('fast');
		$('#money').find('span').fadeOut('fast').html(player.money).fadeIn('fast')
	}
*/
function imagePreload() {
	for(var i=0; i<deck.baseDeck.length; i++) {
		$('#gameField').append('<img class="cardM" src="images/cards/'+deck.baseDeck[i]+'.png" alt="card"/>\n')
	}
}	

function updateState(data)
{
	var newcards = 0;
	var checksplit = 1;

	if(data.errorCode == null || data.errorCode != 0)
	{
		var emsg = "Server Error";
		if(data.errorCode != null)
		{
			emsg = emsg + ": " + data.errorCode;
		}
		alert(emsg);
		return;	
	}
	
	if(data.balance < $('#money').find('span').html() || $('#money').find('span').html() == "")
	{
		$('#money').find('span').fadeOut('fast').html(data.balance).fadeIn('fast');
		$('#bet').find('span').fadeOut('fast').html(data.bet).fadeIn('fast');
	}

	
	if(data.gameID != null)
	{
		$('#gameid').html("Game ID# "+ data.gameID);
	}
///////////////////////////////////////////////////////////////////////////////

	if(data.gameover != 1 && data.showDeal == 0 && data.thisR2 != null && data.thisR2.length > 0 && data.thisHR1RX != null && data.thisHR1RX.length > 0)
	{
		client_seed = data.thisR2;
		server_seed_hash = data.thisHR1RX;
		////$('#clientseed').html(cs_noneditable+'<br>'+client_seed+'</p><p style="text-align:center">Server Random Seed Hash (SHA256(R1+RX)):</p><p style="text-align:center">'+server_seed_hash+'</p>');	
		$('#cpcurrenttext').html("Current Hand");
		$('#csinput').hide();
		$('#cstext').html(client_seed).show();
		$('#cpcthr1rx').html(data.thisHR1RX);
	}
	else
	{

	}
	
	var tempc1 = null;
	var tempTime = null;

	if(data.dcards != null && dealer.cardsShown == 0 && data.gameover == 1)
	{
		tempc1 = data.dcards[1];
		data.dcards[1] = data.dcards[0];
		data.dcards[0] = 52;	
	}

	if(data.dcards != null && data.p1cards != null && dealer.cardsShown == 0 && player1.cardsShown == 0 && data.dcards.length == 2 && data.p1cards.length == 2 && data.p2cards == null && data.p3cards == null)
        {
                if(data.dcards.length >= 2 && data.p1cards.length >= 2)
                {
			showCards('player1', data.p1cards[0], 1);
			newcards++; player1.cardsShown++;
			showCards('dealer', data.dcards[0], 1, null, newcards*cardTime);
			newcards++; dealer.cardsShown++;
			showCards('player1', data.p1cards[1], 2, null, newcards*cardTime);
                        newcards++; player1.cardsShown++;
                        showCards('dealer', data.dcards[1], 2, null, newcards*cardTime);
                        newcards++; dealer.cardsShown++;
			setTimeout(function() {
				$('.curValue.dealer').html(data.dscore);
			},newcards*cardTime); 
                }
        }
	else if(data.dcards != null && dealer.cardsShown == 0)
	{
		tempTime= cardTime;
		cardTime = 0;
		checksplit = 0;
		
		for(var i = dealer.cardsShown; i < data.dcards.length; i++)
		{
			showCards('dealer', data.dcards[i], i+1,null,newcards*cardTime);
			newcards++; dealer.cardsShown++;
		}
		setTimeout( function(){
			$('.curValue.dealer').html(data.p1score);
		},(newcards)*cardTime);

	}


	/*
	if(data.dcards != null && (data.gameover == 0 || dealer.cardsShown == 0) )
	{
		for(var i = dealer.cardsShown; i < data.dcards.length; i++)
		{
			newcards++;
			showCards('dealer', data.dcards[i], i+1, null, (newcards-1)*cardTime);
			dealer.cardsShown++;
		}
		if(tempc1 == null)
		{
			$('.curValue.dealer').html(data.dscore);
		}
	}*/

	if(checksplit == 1)
	{	
		if(data.p3cards != null)
		{
			if(player3.cardsShown == 0 && (player1.cardsShown > 0))
			{
				var p1 = 2;
				//if($('#gameField img').eq(-1).hasClass('player2'))
				//if(data.currentHand == 1)
				if(prevHand == 0)
				{
					p1 = 1;
				}/*
				else if(data.currentHand == 2)
				{
					p1 = 2;
				}*/
				newcards++;
				showCards('player3',null,1,null,(newcards-1)*cardTime,p1);
				player3.cardsShown++;
				if(p1 == 1)
				{
					player1.cardsShown--;
				}
				else
				{
					player2.cardsShown--;
				}
			
			}
		}
	
		if(data.p2cards != null)
		{
			if(player2.cardsShown == 0 && (player1.cardsShown > 0))
			{
				newcards++;
				showCards('player2',null,1,null,(newcards-1)*cardTime,1);
				player2.cardsShown++;
				player1.cardsShown--;
			
			}
		}
	}

	
	if(data.p1cards != null)
	{
		for(var i = player1.cardsShown; i < data.p1cards.length; i++)
		{
			showCards('player1', data.p1cards[i], i+1,null,newcards*cardTime);
			newcards++; player1.cardsShown++;
		}
		setTimeout( function(){
			$('.curValue.player1').html(data.p1score);
		},(newcards)*cardTime);
	}

	if(data.p2cards !=null)
	{
		for(var i = player2.cardsShown; i < data.p2cards.length; i++)
		{
			showCards('player2', data.p2cards[i], i+1,null, newcards*cardTime);
			newcards++; player2.cardsShown++;
		}
		setTimeout( function(){
			$('.curValue.player2').html(data.p2score);
		},(newcards)*cardTime); 
	}

	if(data.p3cards != null)
	{
		for(var i = player3.cardsShown; i < data.p3cards.length; i++)
        	{
			showCards('player3', data.p3cards[i], i+1,null,newcards*cardTime);
			newcards++; player3.cardsShown++;
		}
		setTimeout( function(){
			$('.curValue.player3').html(data.p3score);
		},(newcards)*cardTime);
	}

	if(data.numSplits == 1 && data.gameover == 0)
	{
		if(data.currentHand == 0)
		{
			fadePlayer('player2', newcards*cardTime, 0);
			fadePlayer('player1', newcards*cardTime, 1);
		}
		else
		{
			fadePlayer('player1', newcards*cardTime, 0);
			fadePlayer('player2', newcards*cardTime, 1);
		}
	}
	else if(data.numSplits == 2 && data.gameover == 0)
	{
		if(data.currentHand == 0)
		{
			fadePlayer('player2', newcards*cardTime, 0);
			fadePlayer('player3', newcards*cardTime, 0);
			fadePlayer('player1', newcards*cardTime, 1);
		}
		else if(data.currentHand == 1)
		{
			fadePlayer('player1', newcards*cardTime, 0);
			fadePlayer('player3', newcards*cardTime, 0);
			fadePlayer('player2', newcards*cardTime, 1);
		}
		else
		{
			fadePlayer('player1', newcards*cardTime, 0);
			fadePlayer('player2', newcards*cardTime, 0);
			fadePlayer('player3', newcards*cardTime, 1);
		}
	}

	if(tempTime != null)
	{
		cardTime = tempTime;
		tempTime = null;
		newcards = 0;
	}

	if(data.gameover == 1)
	{
		if(data.numSplits != 0)
		{
			fadePlayer('player1', newcards*cardTime, 1);
			fadePlayer('player2', newcards*cardTime, 1);
			fadePlayer('player3', newcards*cardTime, 1);
		}
		if(tempc1 != null)
		{
			data.dcards[0] = data.dcards[1];
			data.dcards[1] = tempc1;
		}	

		$('.curValue.dealer').html("");
	
		//newcards++;		

		//$('#gameField img')
		$('.cardM.dealer')
		.eq(0)
		//.find('.cardM.dealer')
		//.eq(0)
		.delay(newcards*cardTime)
		//.css('z-index', ++tPos)
		//.delay(newcards*cardTime)
		.animate({
			top: 20,
			right: 325
		},{queue:true, duration : cardTime, complete: function()  {
			$(this).fadeOut(cardTime);
			
			$('#gameField img')
			.eq(data.dcards[1])
			.clone()
			.appendTo('#gameField')
			.css('z-index', ++tPos)
			.addClass('dealer')
	//		.show()		
			.animate({
				top: 20,
				right: 325
			},{queue:false, duration : 0})
			.css('z-index', ++tPos)
			.fadeIn(cardTime);
			
			var j = 1;
			for(var i = dealer.cardsShown; i < data.dcards.length; i++)
			{
				newcards++;
				showCards('dealer', data.dcards[i], i+2,cardTime,j*cardTime);//+j*cardTime);
				dealer.cardsShown++;
				j++;
			}
			/*$('.curValue.dealer').delay((j-1)*1500).queue(function(){ 
				$(this).html(data.dscore);
				updateState2(data);
				$(this).dequeue();
			});*/
			setTimeout(function() {
				$('.curValue.dealer').html(data.dscore);
				updateState2(data);
			//	setTimeout(function() {
					$.post("control", { "func":"getState","bet":1}, updateState,"json");
			//	}, j*cardTime);
			}, (j)*cardTime);
		
		}});		 
		
	}
	else
	{
		setTimeout(function(){
			updateState2(data);
		}, newcards*cardTime);
	}
	

}

function updateState2(data)
{
	if(data.balance > $('#money').find('span').html())
	{
		$('#money').find('span').fadeOut('fast').html(data.balance).fadeIn('fast');
		//$('#bet').find('span').fadeOut('fast').html(data.bet).fadeIn('fast');
	}

	maxbet = data.maxbet;
	minbet = data.minbet;

	if(data.gameover == 1)
	{
		////$('#serverseed').html('<p style="text-align:center">Most Recently Finished Hand:</p><table id="sstable"><tr class="even"><td>Game ID</td><td>'+data.gameID+'</td></tr><tr class="odd"><td>Server Random Seed (R1)</td><td>'+data.thisR1+'</td></tr><tr class="even"><td>Server RX value</td><td>'+data.thisRX+'</td></tr><tr class="odd"><td>SHA256(R1+RX)</td><td>'+data.thisHR1RX+'</td></tr><tr class="even"><td>Client Random Seed (R2)</td><td>'+data.thisR2+'</td></tr></table>');
		$('#cpltgid').html(data.gameID);
		$('#cpltr1').html(data.thisR1);
		$('#cpltrx').html(data.thisRX);
		$('#cplthr1rx').html(data.thisHR1RX);
		$('#cpltr2').html(data.thisR2);

	        //$('#serverseed').html('<p style="text-align:center;font-weight:bold">Most Recently Finished Hand:</p><p style="text-align:center">Server Random Seed (R1): '+data.thisR1+'</p><p style="text-align:center">RX value: '+data.thisRX+'</p><p style="text-align:center">SHA256(R1+RX): '+data.thisHR1RX+'</p><p style="text-align:center">R2 value: '+data.thisR2+'</p>');

		var myBet = Number(data.bet);
		var myMoney = Math.floor(Number($('#money').find('span').html()));

		if(myBet > myMoney || myBet > maxbet)
		{
			if(myMoney > maxbet)
			{
				myBet = maxbet;
			}
			else
			{
				myBet = myMoney;
			}
			$('#bet').find('span').fadeOut('fast').html(myBet).fadeIn('fast');
		}
	}
	
	//$('.curValue.player2').html(data.p2score);
	//$('.curValue.player3').html(data.p3score);
	
	if(data.msg.length > 0)
	{
		$('#msg').slideToggle('fast').html(data.msg);
		  
//		setTimeout(function() {
//			$('#msg').fadeOut('fast');
//		}, 4500);
	}
	if(data.p1msg.length > 0)
	{
		$('#p1msg').slideToggle('fast').html(data.p1msg);
		  
//		setTimeout(function() {
//			$('#p1msg').fadeOut('fast');
//		}, 4500);
	}
	if(data.p2msg.length > 0)
	{
		$('#p2msg').slideToggle('fast').html(data.p2msg);
		  
//		setTimeout(function() {
//			$('#p2msg').fadeOut('fast');
//		}, 4500);
	}
	if(data.p3msg.length > 0)
	{
		$('#p3msg').slideToggle('fast').html(data.p3msg);
		  
//		setTimeout(function() {
//			$('#p3msg').fadeOut('fast');
//		}, 4500);
	}
	
/*	var bc = 0;
	if(data.showDeal == 1) { bc++; }
	if(data.showHit == 1) { bc++; }
	if(data.showStay == 1) { bc++; }
	if(data.showDouble == 1) { bc++; }
	if(data.showSplit == 1) { bc++; }
	var marg;
	if(bc >= 1)
	{
		marg = Math.floor((350-(bc*61))/(bc+1));
	}
	if(data.showDeal == 1) { $('#deal').css('margin-left', marg+"px").show(); } else { $('#deal').hide(); }
	if(data.showHit == 1) { $('#hit').css('margin-left', marg+"px").show(); } else { $('#hit').hide(); }
	if(data.showStay == 1) { $('#stay').css('margin-left', marg+"px").show(); } else { $('#stay').hide(); }
	if(data.showDouble == 1) { $('#double').css('margin-left', marg+"px").show(); } else { $('#double').hide(); }
	if(data.showSplit == 1) { $('#split').css('margin-left', marg+"px").show(); } else { $('#split').hide(); }
*/
	if(data.showDeal == 1)
	{ 
		//client_seed = Math.random();
		client_seed = genSeed();
		server_seed_hash = data.nextHR1RX;
		////$('#clientseed').html(cs_editable+'<p style="text-align:center">Next Hand\'s Server Random Seed Hash (SHA256(R1+RX)):</p><p style="text-align:center">'+data.nextHR1RX+'</p>');		
		////$('#cs').val(""+client_seed);
		$('#cpcurrenttext').html("Next Hand:");
		$('#cstext').hide();
		$('#csinput').val(client_seed).show();
		$('#cpcthr1rx').html(data.nextHR1RX);

		if(data.showHit != 0 || data.showStay != 0 || data.showDouble != 0 || data.showSplit != 0)
		{
			alert("Server Display Error 1");
			return;
		}
		$('#deal').css('margin-left', "144px").show();
		$('.chip').show();
	}
	else
	{
//		$('#deal').hide();
		if(data.showHit == 1)
		{
			if(data.showStay != 1)
			{
				alert("Server Display Error 2");
				return;
			}
			if(data.showDouble == 1)
			{
				$('#double').css('margin-left', "21px").show();
				$('#hit').css('margin-left', "21px").show();
			}
			else
			{
				$('#hit').css('margin-left', "103px").show();
			}
			$('#stay').css('margin-left', "21px").show();
			if(data.showSplit == 1)
			{
				$('#split').css('margin-left', "21px").show();
			}
		}
	}

	prevHand = data.currentHand;
}

function fadePlayer(player, delay, op)
{
	if(op == 0)
	{
		op=0.2;
	}
	$('.cardM.'+player)
	//.delay(delay)
	.css('opacity',op);
}
