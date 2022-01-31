# URL Shortener
URL Shortener предназначен для сокращения сложных длинных ссылок на короткие и простые.

Например: https://www.youtube.com/watch?t=3&v=DLzxrzFCyOs&feature=youtu.be => http://arcane-fortress-64578.herokuapp.com/p2 

🔗 [Demo API](http://arcane-fortress-64578.herokuapp.com/api)

## Примеры запросов

### Получение всех ссылок

`GET /links`

    curl --location --request GET 'https://arcane-fortress-64578.herokuapp.com/api/links'

**Фильтрация ссылок по названию и тегам**
   
    curl --location -g --request GET 'https://arcane-fortress-64578.herokuapp.com/api/links?filter[title]=Cool&filter[tag]=search_engines'

### Создание ссылки

`POST /links`

    curl --location --request POST 'https://arcane-fortress-64578.herokuapp.com/api/links' --header 'Content-Type: application/json' --data-raw '{"long_url": "https://google.com","title": "Cool link to google","tags": ["search_engines","google"]}'

**Несколько ссылок**

    curl --location --request POST 'https://arcane-fortress-64578.herokuapp.com/api/links' --header 'Content-Type: application/json' --data-raw '[{"long_url": "https://yandex.com","title": "Cool link to yandex","tags": ["search_engines","google"]},{"long_url": "https://google.com","title": "Cool link to google","tags": ["search_engines","google"]}]'

### Обновление информации о ссылке

`PATH /links/{id}`

    curl --location --request PATCH 'https://arcane-fortress-64578.herokuapp.com/api/links/10' --header 'Content-Type: application/json' --data-raw '{"title": "Google","tags": ["search_engines","google"]}'
    
    
### Удаление ссылки

`DELETE /links/{id}`

    curl --location --request DELETE 'https://arcane-fortress-64578.herokuapp.com/api/links/10'

### Получение ссылки по id

`GET /links/{id}`

    curl --location --request GET 'https://arcane-fortress-64578.herokuapp.com/api/links/11'

### Получение статистики ссылки

`GET /stats/{id}`

    curl --location --request GET 'https://arcane-fortress-64578.herokuapp.com/api/stats/11'

### Получение общей статистики

`GET /stats`

    curl --location --request GET 'https://arcane-fortress-64578.herokuapp.com/api/stats'
