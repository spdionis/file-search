# Over-engineered file search command

To test the project the easiest way is using vagrant and virtualbox.

After installing virtualbox and vagrant:

1. Run `vagrant up` in project's root directory
2. Ssh into the box with `vagrant ssh`
3. Go to project root inside box, `cd /vagrant`
4. run `vendor/bin/phpunit` or to test manually `php bin/console fs:file-search ...`
