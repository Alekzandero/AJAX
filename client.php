<?php
	header("Expires: 0");
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Pragma: no-cache"); // HTTP/1.0
	header('Content-Type: text/html; charset=utf-8');
?>
<html>
    <head>
        <script type="text/javascript">
            // Глобальные переменные
            var counter = 0;

            // Вызывается после полной загрузки страницы
            window.onload = function(e) 
            {
                if (!Date.now) {
                    Date.now = function now() {
                        return new Date().getTime();
                    }
                }
            }

            // Функция обработки AJAX-события
            function ajaxFunction()
            {
                var xmlhttp;

                // Создаем обект для работы с AJAX
                if (window.XMLHttpRequest) {
                    // code for IE7+, Firefox, Chrome, Opera, Safari
                    xmlhttp=new XMLHttpRequest();
                } else if (window.ActiveXObject) {
                    // code for IE6, IE5
                    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
                } else {
                    alert("Ваш браузер устарел! Также вероятно, что работа с AJAX отключена в настройках безопасности. Работа с сервисом невозможна.");
                }

                // Что отправляем на сервер
                //var params = "hello=world&good=morning"; // POST
                var params = JSON.stringify({hello:"world", good:"morning"}); // JSON

                // Открываем POST-запрос к серверу
                xmlhttp.open("POST", "server.php", true); //POST и JSON

                // Определяем параметры работы с сервером
                // POST-метод:
                // xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                // xmlhttp.setRequestHeader("Content-length", params.length);
                // xmlhttp.setRequestHeader("Connection", "close" );
                // JSON-метод:
                xmlhttp.setRequestHeader('Content-type','application/json; charset=utf-8');

                // Делаем запрос к серверу
                xmlhttp.send(params);

                // Функция, которая отрабатывает когда завершена загрузка с сервера
                xmlhttp.onreadystatechange=function()
                {
                    if(xmlhttp.readyState == 4) {
                        // Обрабатываем полученные от сервера данные
                        // POST-метод:
                        // document.getElementById("result").innerHTML = xmlhttp.responseText;
                        // JSON-метод:
                        var data = JSON.parse(xmlhttp.responseText);
                        document.getElementById("result").innerHTML = data["hello"];
                    }
                }
            }

            // Получаем данные таблицы в виде массива
            function getTableData_v1(table_id)
            {
                var table = document.getElementById(table_id);
                var res = {};//new Array();// = new Array(table.rows.length);
                for(var i = 0, row; row = table.rows[i]; i++) {
                    res['row_'+i] = new Array(row.cells.length);
                    for(var j = 0, col; col = row.cells[j]; j++) {
                        res['row_'+i][j] = col.getElementsByTagName('input')[0].value;
                    }
                }
                return res;
            }

            function getTableData(table_id)
            {
                var table = document.getElementById(table_id);
                var res = new Array(table.rows.length);
                for(var i = 0; i < table.rows.length; i++) {
                    var row; row = table.rows[i];
                    res[i] = new Array(row.cells.length);
                    for(var j = 0; j < row.cells.length; j++) {
                        var cell; cell = row.cells[j];
                        //res[i][j] = cell.children[0].value;
                        res[i][j] = cell.getElementsByTagName('input')[0].value;
                    }
                }
                return res;
            }

            // Вывести содержимое формы
            function printFormData()
            {
                // alert(getTableData_v1("editor"));
                document.getElementById("table_result").innerHTML = JSON.stringify(getTableData_v1("editor"), null, 4);
                console.log(getTableData_v1("editor"));
                //alert(Date.now());
            }

            // Добавить строку в таблицу
            function addTableRow()
            {
                var table = document.getElementById("editor");
                var row = table.insertRow(table.rows.length);
                var cell;
                cell = row.insertCell(0); cell.innerHTML = '<input type="text" id="nick" name="nickname" />';//Date.now();
                cell = row.insertCell(1); cell.innerHTML = '<input type="text" name="update_result" size="100" />';//counter++;
                return 0;
            }

        </script>
    </head>
    <body>
        Эта форма:<br>
        <form name="myForm">
            <table id="editor">
                <tr>
                    <td><input type="text" id="nick" name="nickname" /></td>
                    <td><input type="text" name="update_result" size="100" /></td>
                </tr>
            </table>
        </form>
        <a href="javascript:0" onclick="addTableRow();">Добавить строку</a><br>
        <a href="javascript:0" onclick="ajaxFunction();">Отправить на сервер</a><br>
        <a href="javascript:0" onclick="printFormData();">Вывести содержимое матрицы</a><br>
        <pre><div id="result"></div></pre><br>
        <pre><div id="table_result"></div></pre>
    </body>
</html>