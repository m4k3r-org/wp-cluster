function wp_social_stream_ordering_home( results ){
  var instagrams = [];
  var twitters = [];
  var mixed_results = [];

  for( i = 0; i < results.length; i++ ){

    switch( results[i].type ){
      case 'instagram':
        instagrams.push( results[i] );
        break;
      case 'twitter':
        twitters.push( results[i] );
        break;
    }

  }

  instagrams = instagrams.reverse();
  twitters = twitters.reverse();

  i = 0;

  while( instagrams.length != 0 || twitters.length != 0 ){

    if( i % 5 == 0 ){
      elm = instagrams.pop();
    } else{
      elm = twitters.pop();
    }

    if( typeof(elm) != 'undefined' ){
      mixed_results.push( elm );
    }

    i++;
  }

  return mixed_results;
}