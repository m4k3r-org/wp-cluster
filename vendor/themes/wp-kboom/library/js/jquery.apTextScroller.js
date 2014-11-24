(function($) {

apTextScroller = function() {
	
	var _textField;
	var _scrolingSpeed;//speed which text moves (its enter frame based)
	var _maskWidth;
	var _spaceString;//text inbetween main text
	var _oneLength;
	var _direction;
	var scrollInterval = 100;
	var scrollIntervalID;
	var scrollValue=0;
	var fontMeasure;
	
	//**************** CONSTRUCTOR
	
	this.init = function(fm, txt, mask, direction, spaceString, scrolingSpeed){
		fontMeasure=fm;
		_textField=txt;
		_maskWidth=mask.width();
		_direction=direction;
		_spaceString=spaceString;
		_scrolingSpeed=scrolingSpeed;
		//console.log('_maskWidth = ', _maskWidth);
	}	
	
	//**************** PUBLIC
	
	this.input = function(s) {
		//console.log('s = ', s);
		fontMeasure.html(s);
		//var w = fontMeasure.width();
		//var h = fontMeasure.height();
		//console.log(w, ', ', h);
		
		var noSpace = s.replace(/\s/g, '&nbsp;');
		//console.log(noSpace);
		var t=noSpace + _spaceString;
		//console.log(t);
		
		_textField.html('');//reset
		_textField.css('width', 'auto');
		_textField.html(t);
		_oneLength=_textField.width();
		//console.log('_oneLength = ', _oneLength);
		if(_oneLength == 0) return;
		var size;
		var z=t;
		//we need to append so many times that its at least 2 oneLength's long.
		if (_oneLength <= _maskWidth * 2) {
			//trace(visibleSpace * 2 / oneLength);
			var limit=Math.floor(_maskWidth * 2 / _oneLength);///we lower it down because we already have one text inside.
			//console.log('limit = ', limit);
			for (var i=0; i < limit; i++) {
				z+=t;
			}
			size=_oneLength*(limit+1);
		} else {//the above loop didnt handle case if text is longer than visibleSpace * 2!,   then it wouldnt be copied at all and it still need to be copied once.
			z+=t;
			size=_oneLength*2;
		}
		_textField.html(z);
		_textField.css('width', size+1+'px');

		/*if(_direction == "right"){//reverse words
		
		
		}*/
	}
	
	this.inputSingle = function(s) {
		//_textField.text = s;
		//_textField.x = 0;
	}
	
	this.activate = function() {
		if(scrollIntervalID) clearInterval(scrollIntervalID);
		scrollIntervalID = setInterval(scrollText, scrollInterval);	
	}
	
	this.deactivate = function() {
		if(scrollIntervalID) clearInterval(scrollIntervalID);
		if(_direction == "left"){//reset
			_textField.css('left', 0+'px');
			scrollValue=0;
		}
	}
		
	//**************** PRIVATE
	
	function scrollText() {
		if (_direction == "left") {
			scrollValue -= _scrolingSpeed;
			//console.log(scrollValue);
			_textField.css('left', scrollValue+'px');
			if(parseInt(_textField.css('left'),10) < -_oneLength) {
				scrollValue=-_scrolingSpeed;//reset
			}
		} else {
			/*_textField.x+= _scrolingSpeed;
			if (_textField.x > 0) {
				_textField.x=- _oneLength + _scrolingSpeed;// because it should accomodate for the shift
			}*/
		}
	}
}

})(jQuery);