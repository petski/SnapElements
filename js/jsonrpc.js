function jsonrpc_call(method, params, id) { 
	var obj = false;
	new Ajax.Request('api/jsonrpc.php', {
                          method: 'post',
                          parameters: {"jsonrpc": "2.0", "method": method, "params": params, "id": id},
                          onSuccess: function(transport) {
						obj = transport.responseJSON;
                          }
                        });
	return obj;
}
