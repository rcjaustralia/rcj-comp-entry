function cePost(path, params) 
{
    var form = document.createElement("form");

    form.setAttribute("method", "post");
    form.setAttribute("action", path);

    for(var key in params) {
        if(params.hasOwnProperty(key)) {
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", params[key]);
            form.appendChild(hiddenField);
         }
    }

    document.body.appendChild(form);
    form.submit();
}

function CEPostDelete(AUID, APath)
{
   if (typeof APath === 'undefined') { APath = 'delete.php'; }
   cePost(APath, {uid: AUID} ); 
}

function CEPostEdit(AUID, APath)
{
   if (typeof APath === 'undefined') { APath = 'edit.php'; }
   cePost(APath, {action: 'EDIT', uid: AUID} ); 
}

function CEPostNew(APath)
{
   if (typeof APath === 'undefined') { APath = 'edit.php'; }
   cePost(APath, {action: 'NEW'} ); 
}

