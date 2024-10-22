## Test task:
Напишіть Symfony-консольну команду, яка отримує курси валют з приватбанку та монобанку, а потім перевіряє, чи вони змінилися на більшу або меншу величину, ніж заданий трешхолд. Якщо це так, відправте повідомлення (наприклад, SMS або електронний лист) з інформацією про зміну курсу.


## Installation
### Create a copy of `.env.local` file and add your own settings. Your new file should be named as `.env` to be ignored by git

### Run docker
```shell
docker-compose up -d
```

### Go inside php container
```shell
docker exec -it symfony_php bash
```

### Run the course command
```php bin/console app:check-currency-rates```
```text 
You can add the integer value for your threshold. 
It is an optional parameter, to skip it, press ENTER. 
The default value is 5%.
```