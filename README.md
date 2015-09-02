# test
## API системы вырезания видео

## Базовый URL
/index.php/video

## GET запрос
Возвращает ссписок всез видео пользователя, выбирается по API Key из заголовка запроса X_API_KEY
[{"_id":{"$id":"55e7616948177e26518b4568"},"user":"FSDIFSID3333saf","status":"scheduled"},{"_id":{"$id":"55e770ed48177e27518b4569"},"user":"FSDIFSID3333saf","status":"scheduled"},{"_id":{"$id":"55e771e148177e28518b4568"},"user":"FSDIFSID3333saf","status":"scheduled"}}]


## POST Запрос
Формирует заявку на укорачивание видео.
Передаваемые параметры:
start - время начала нового видео
end  -  время конца нового видео
video - видеофайл


## Логика работы
Клиент  авторизуется на сервере через API_KEY, отправляет запрос на обрезание видео, запрос формирует запись в MongoDB с ID видео и ID пользователя, видео файл кодируется в base64  и вместе с временными метками попадает в очередь video в RabbitMQ. 
Обработчик очереди /worker/processvideo получает данные из очереди, устанавливает флаг "в обработке" на видео, выполняет манипуляции с видео и формирует изменения в БД, дополняя ее ссылкой на видео и продолжительностью.