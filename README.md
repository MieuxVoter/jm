# Web page for Majority judgment (SYMFONY) 

All this project is in French only for the moment.

This project use this package for MajorityJudgement : [Majority Judgement PHP](https://github.com/oceanBigOne/MajorityJudgment)
See more details on majority jugement here : [Wikipedia](https://en.wikipedia.org/wiki/Majority_judgment).



# How to install ?

- Deploy projet on your webserver with git (or FTP width ``develop branch`` for the moment !).

With git :
```
git clone https://github.com/oceanBigOne/jm.git .
``` 

- Reset projet on develop branch : 

```
git reser --hard origin/develop
```

- Create ``.env``  file with ``.env.dist`` model 

  Change access to your database in this line :
  ```
  DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name
  ```
  Change SALT_KEY with any string you want (!important for "hidding" link of vote)
  ```
  SALT_KEY=XXXXXXXXXXXXXXXXXXXXXXX;
  ```
  And set APP_ENV to "prod"
  ```
  APP_ENV=prod
  ```
  
- Then deploy BDD with :
```
php bin/console doctrine:migrations:migrate
```

- Create ``public/.htaccess`` with ``public/.htaccess.sample`` (check if config of your sever require somes changes)

- And clear symfony cache :
```
php bin/console cache:clear --no-warmup -e prod
```

That's all...

## Versions

#### 1.1.5
- Create .htaccess.sample

#### 1.1.4
- update htaccess : "force" HTTPS

#### 1.1.3
- License

#### 1.1.2
- fix version in readme 

#### 1.1.1
- spelling errors

### 1.1.0
- User interface update

### 1.0.0
- Initial version





