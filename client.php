<?php
	// Отключаем кэширование
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
                    xmlhttp = new XMLHttpRequest();
                } else if (window.ActiveXObject) {
                    // code for IE6, IE5
                    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                } else {
                    alert("Ваш браузер устарел! Также вероятно, что работа с AJAX отключена в настройках безопасности. Работа с сервисом невозможна.");
                }

                // Что отправляем на сервер
                var params = JSON.stringify(getTableData("editor"));

                // Открываем POST-запрос к серверу
                xmlhttp.open("POST", "server.php", true);

                // Определяем параметры работы с сервером
                xmlhttp.setRequestHeader('Content-type', 'application/json; charset=utf-8');

                // Делаем запрос к серверу
                xmlhttp.send(params);

                // Функция, которая отрабатывает когда завершена загрузка с сервера
                xmlhttp.onreadystatechange = function()
                {
                    if(xmlhttp.readyState == 4) {

                        // Обрабатываем полученные от сервера данные
                        var data = JSON.parse(xmlhttp.responseText);
                        setTableData("editor", data);
                        document.getElementById("result").innerHTML = JSON.stringify(data, null, 4);
                    }
                }
            }

            // Получаем данные таблицы в виде массива
            function getTableData(table_id)
            {
                var table = document.getElementById(table_id);
                var res = Array();
                for(var i = 0, row; row = table.rows[i]; i++) {
                    res[i] = new Array(row.cells.length);
                    for(var j = 0, col; col = row.cells[j]; j++) {
                        res[i][j] = col.getElementsByTagName('input')[0].value;
                    }
                }
                document.getElementById("table_data").innerHTML = JSON.stringify(res, null, 4);
                return res;
            }

            // Загружаем данные с сервера в таблицу
            function setTableData(table_id, table_data)
            {
                var table = document.getElementById(table_id);
                
                // Удаляем строки таблицы
                while (table.rows.length > 0)
                {
                    table.deleteRow(0);
                }

                // Проставляем данные в таблицу
                for(var i = 0; i < table_data.length; i++)
                {
                    var row = table.insertRow(table.rows.length);
                    row.insertCell(0).innerHTML = '<input type="hidden" value="'+table_data[i][0]+'" />'+table_data[i][0];
                    for(var j = 1; j < table_data[i].length; j++)
                    {
                        var cell = row.insertCell(j);
                        cell.innerHTML = '<input type="text" value="'+table_data[i][j]+'" />';
                    }
                }
                
                return 0;
            }

            // Вывести содержимое формы
            function printFormData()
            {
                document.getElementById("table_result").innerHTML = JSON.stringify(getTableData("editor"), null, 4);
            }

            // Добавить строку в таблицу
            function addTableRow()
            {
                var table = document.getElementById("editor");
                if(table.rows.length == 0) {
                    alert("В таблице должна быть хотя бы одна строка, иначе не понятно сколько столбцов создавать!");
                    return 1;
                }
                
                // Добавляем строку с полем для индекса. Строка содержит столько же столбцов, сколько первая
                var row = table.insertRow(table.rows.length);
                row.insertCell(0).innerHTML = '<input type="hidden" value="" />Новая строка';
                for (var j = 1; j < table.rows[0].cells.length; j++)
                {
                    row.insertCell(j).innerHTML = '<input type="text" />';
                }

                return 0;
            }

        </script>
    </head>
    <body>
        Эта форма:<br>
        <form name="myForm">
            <table id="editor">
                <!--<tr>
                    <td><input type="text" id="nick" name="nickname" /></td>
                    <td><input type="text" name="update_result" size="100" /></td>
                </tr>-->
            </table>
        </form>
        <a href="javascript:0" onclick="addTableRow();">Добавить строку</a><br>
        <a href="javascript:0" onclick="ajaxFunction();">Отправить на сервер</a><br>
        <a href="javascript:0" onclick="printFormData();">Вывести содержимое матрицы</a><br>
        <pre><div id="result"></div></pre><br>
        <pre><div id="table_result"></div></pre>
        Current table data:
        <pre><div id="table_data"></div></pre>
    </body>
</html>
