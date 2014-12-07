/**
 * Universal XMLHttpRequest Client
 *
 * Copyright © 2012 Usability Dynamics, Inc. (usabilitydynamics.com)
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL Alexandru Marasteanu BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

(function () {

  "use strict";

  var

    root = this,
    ejs;

  if (typeof exports !== 'undefined') {
    ejs = exports;
  } else {
    if ( typeof root.ejs === 'undefined') {
      ejs = root.ejs = {};
    } else {
      ejs = root.ejs;
    }
  }

  /**
   * Http Client
   * @param {type} server
   * @returns {_L5.ejs.HttpClient.Anonym$0}
   */
  ejs.HttpClient = function ( server ) {
    var

      /**
       * Predefined headers
       * @type type
       */
      headers = {
        'Content-Type': 'application/json'
      },

      /**
       * Method to ensure the path always starts with a slash
       * @param {type} path
       */
      getPath = function (path) {
        if (path.charAt(0) !== '/') {
          path = '/' + path;
        }

        return server + path;
      },

      /**
       * Cross-browser xmlhttprequest init
       */
      getXmlHttp = function() {
        var xmlhttp;
        try {
          xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
          try {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
          } catch (E) {
            xmlhttp = false;
          }
        }
        if (!xmlhttp && typeof XMLHttpRequest !== 'undefined') {
          xmlhttp = new XMLHttpRequest();
        }
        return xmlhttp;
      },

      /**
       * Method to set additional headers
       * @param {type} XHR
       * @param {type} headers
       * @returns {undefined}
       */
      setHeaders = function( XHR, headers ) {
        for( var i in headers ) {
          XHR.setRequestHeader( i, headers[i] );
        }
      };

    /**
     * Check that the server path does no end with a slash
     */
    if (server === null) {
      server = '';
    } else if (server.charAt(server.length - 1) === '/') {
      server = server.substring(0, server.length - 1);
    }

    /**
     * Return instance with public methods
     */
    return {

      /**
       * Server endpoint
       * @param {type} s
       * @returns {_L5.ejs.HttpClient.Anonym$0|_L5.ejs.HttpClient.Anonym$0.server}
       */
      server: function (s) {
        if (s === null) {
          return server;
        }

        if (s.charAt(s.length - 1) === '/') {
          server = s.substring(0, s.length - 1);
        } else {
          server = s;
        }

        return this;
      },

      /**
       * Adds new header
       * @param {type} name
       * @param {type} value
       * @returns {undefined}
       */
      addHeader: function ( name, value ) {
        headers[name] = value;
      },

      /**
       * Fires GET request
       * @param {type} path
       * @param {type} data
       * @param {type} successcb
       * @param {type} errorcb
       * @returns {unresolved}
       */
      get: function (path, data, successcb, errorcb) {
        var XHR = getXmlHttp();
        XHR.open('GET', getPath(path)+'?'+data, true);
        setHeaders( XHR, headers );
        XHR.onload = function(e) {
          if ( e.target.readyState === 4 ) {
            if( e.target.status >= 200 && e.target.status < 300 || e.target.status === 304 ) {
              successcb(JSON.parse(e.target.response), e.target);
            } else {
              errorcb(JSON.parse(e.target.response), e.target);
            }
          }
        };
        XHR.onerror = function(e) {
          errorcb(e.target);
        };
        XHR.send(null);
        return XHR;
      },

      /**
       * Fires POST request
       * @param {type} path
       * @param {type} data
       * @param {type} successcb
       * @param {type} errorcb
       * @returns {unresolved}
       */
      post: function (path, data, successcb, errorcb) {
        var XHR = getXmlHttp();
        XHR.open('POST', getPath(path), true);
        setHeaders( XHR, headers );
        XHR.onload = function(e) {
          if ( e.target.readyState === 4 ) {
            if( e.target.status >= 200 && e.target.status < 300 || e.target.status === 304 ) {
              successcb(JSON.parse(e.target.response), e.target);
            } else {
              errorcb(e.target.response, e.target);
            }
          }
        };
        XHR.onerror = function(e) {
          errorcb(e.target);
        };
        XHR.send(data);
        return XHR;
      },

      /**
       * Fires PUT request
       * @param {type} path
       * @param {type} data
       * @param {type} successcb
       * @param {type} errorcb
       * @returns {unresolved}
       */
      put: function (path, data, successcb, errorcb) {
        var XHR = getXmlHttp();
        XHR.open('PUT', getPath(path), true);
        setHeaders( XHR, headers );
        XHR.onload = function(e) {
          if ( e.target.readyState === 4 ) {
            if( e.target.status >= 200 && e.target.status < 300 || e.target.status === 304 ) {
              successcb(JSON.parse(e.target.response), e.target);
            } else {
              errorcb(JSON.parse(e.target.response), e.target);
            }
          }
        };
        XHR.onerror = function(e) {
          errorcb(e.target);
        };
        XHR.send(data);
        return XHR;
      },

      /**
       * Fires DELETE request
       * @param {type} path
       * @param {type} data
       * @param {type} successcb
       * @param {type} errorcb
       * @returns {unresolved}
       */
      delete: function (path, data, successcb, errorcb) {
        var XHR = getXmlHttp();
        XHR.open('DELETE', getPath(path), true);
        setHeaders( XHR, headers );
        XHR.onload = function(e) {
          if ( e.target.readyState === 4 ) {
            if( e.target.status >= 200 && e.target.status < 300 || e.target.status === 304 ) {
              successcb(JSON.parse(e.target.response), e.target);
            } else {
              errorcb(JSON.parse(e.target.response), e.target);
            }
          }
        };
        XHR.onerror = function(e) {
          errorcb(e.target);
        };
        XHR.send(data);
        return XHR;
      },

      /**
       * Fires HEAD request
       * @param {type} path
       * @param {type} data
       * @param {type} successcb
       * @param {type} errorcb
       * @returns {jqXHR}
       */
      head: function (path, data, successcb, errorcb) {
        var XHR = getXmlHttp();
        XHR.open('HEAD', getPath(path), true);
        setHeaders( XHR, headers );
        XHR.onload = function(e) {
          if ( e.target.readyState === 4 ) {
            if( e.target.status >= 200 && e.target.status < 300 || e.target.status === 304 ) {
              var headers = e.target.getAllResponseHeaders().split('\n'),
                resp = {},
                parts,
                i;

              for (i = 0; i < headers.length; i++) {
                parts = headers[i].split(':');
                if (parts.length !== 2) {
                  resp[parts[0]] = parts[1];
                }
              }

              if (successcb !== null) {
                successcb(resp);
              }
            } else {
              errorcb(JSON.parse(e.target.response), e.target);
            }
          }
        };
        XHR.onerror = function(e) {
          errorcb(e.target);
        };
        XHR.send(data);
        return XHR;
      }
    };
  };

}).call(this);