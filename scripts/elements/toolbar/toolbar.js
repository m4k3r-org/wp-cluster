define(
  [
    'global',
    'element/toolbar/viewModel',
    'text!element/toolbar/template.html'
  ],
  function( _ddp, ViewModel, Template ){
    'use strict';
    return {
      viewModel: ViewModel,
      template: Template
    };
  }
);
