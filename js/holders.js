function getHolders(){
    
    var f = {'ph':null};

    f['ph'] = (function(){ 
        var instance, 
        serv = 'http://' + window.location.hostname + '/',
        _pathArr = {
            'name' : '',
            'exp' : ''
        };//private property

              //public method
        function init(dir){
            return {
                name: function(nameV){
                    if(_pathArr['exp'] && _pathArr['exp'].length > 0){
                        if (arguments.length === 0) {
                            return _pathArr['name'];
                        }
                        else {
                            _pathArr = path_holder(serv + dir + nameV + '.' + _pathArr['exp']);
                            return true;
                        }
                    }
                    else return false;
                },
                full_name: function(nameV){
                    if (arguments.length === 0) {
                        if(_pathArr['exp'] && _pathArr['exp'].length > 0){
                            return _pathArr['name'] + "." + _pathArr['exp'];
                        }
                        else return false;
                    }
                    else{
                        _pathArr = path_holder(serv + dir + nameV);
                        if(_pathArr['exp'] && _pathArr['exp'].length > 0) {
                            return true;
                        }
                        else return false;
                    }
                },
                full_path: function(pathV){
                    if (arguments.length === 0) {
                        if(_pathArr['exp'] && _pathArr['exp'].length > 0){   
                            return serv + dir + _pathArr['name'] + "." + _pathArr['exp'];
                        }
                        else return false;
                    }
                    else {
                        _pathArr = path_holder(pathV);
                        return true;
                    }

                },
                path: function(pathV){              
                    if (arguments.length === 0){
                        if(_pathArr['exp'] && _pathArr['exp'].length > 0){            
                            return dir + _pathArr['name'] + "." + _pathArr['exp'];
                        }
                        else return false;  
                    }
                        else {
                            _pathArr = path_holder(pathV);
                            return true;
                    }
                }

            };
        }

        return {
            getInstance: function (dir) {
                if ( !instance ) {
                  instance = init(dir);
                }
                return instance;
            }
        };
    })().getInstance('images/carousel/');



    function translite(str){

        var arr={'а':'a', 'б':'b', 'в':'v', 'г':'g', 'д':'d', 'е':'e', 'ж':'g', 'з':'z',
            'и':'i', 'й':'y', 'к':'k', 'л':'l', 'м':'m', 'н':'n', 'о':'o', 'п':'p', 'р':'r',
            'с':'s', 'т':'t', 'у':'u', 'ф':'f', 'ы':'i', 'э':'e', 'А':'A', 'Б':'B', 'В':'V',
            'Г':'G', 'Д':'D', 'Е':'E', 'Ж':'G', 'З':'Z', 'И':'I', 'Й':'Y', 'К':'K', 'Л':'L',
            'М':'M', 'Н':'N', 'О':'O', 'П':'P', 'Р':'R', 'С':'S', 'Т':'T', 'У':'U', 'Ф':'F',
            'Ы':'I', 'Э':'E', 'ё':'yo', 'х':'h', 'ц':'ts', 'ч':'ch', 'ш':'sh', 'щ':'shch',
            'ъ':'', 'ь':'', 'ю':'yu', 'я':'ya', 'Ё':'YO', 'Х':'H', 'Ц':'TS', 'Ч':'CH', 'Ш':'SH',
            'Щ':'SHCH', 'Ъ':'', 'Ь':'', 'Ю':'YU', 'Я':'YA', 'Ї':'YI', 'ї':'yi', 'І':'I', 'і':'i'  };

        var replacer = function(a){return arr[a]||a;};
        str = str.replace(/\s|\./g, "_");
        str = str.replace(/:|;/g, "");
        return str.replace(/[А-яёЁїЇіІ]/g,replacer);

    }

    function path_holder(pathV){      
        var dirS, nameE;
        var dirV, nameV, expV;

            dirS = pathV.split("/");
            nameE = dirS[dirS.length - 1];
            dirS[dirS.length - 1] = null;
            dirV = dirS.join("/");
            nameE = nameE.split(".");
            expV = nameE[nameE.length - 1];
            nameE[nameE.length - 1] = null;
            nameV = nameE.join("_");       
            nameV = nameV.replace(/_+$/g, "");
            nameV = translite(nameV);

        return {
            'name' : nameV,
            'exp' : expV
        };
    }



      return f;
}


