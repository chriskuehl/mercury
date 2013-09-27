#!/usr/bin/env python3
import sys, json

def get_cleaned_json(path):
	lines = [line.strip() for line in open(path)]
	clean_lines = []
	
	for line in lines:
		if '#' in line:
			line = line[:line.index('#')]
		
		clean_lines.append(line)
	
	return '\n'.join(clean_lines)


# load configuation from JSON
config_path = sys.argv[1]
config_path_parts = config_path.split('/')

user = config_path_parts[0]
domain = config_path_parts[1]

config = json.loads(get_cleaned_json(config_path))

sys.stderr.write('Updating: {} ({})\n'.format(domain, user))

# generate new VirtualHost zones
#### redirect vhost
print("<VirtualHost *:8080 *:8443>")

# logging
print("	CustomLog {}access.log vhost_combined".format(config['logPath']))
print("	ErrorLog {}error.log".format(config['logPath']))
print("	CustomLog /var/log/apache2/access.log vhost_combined")
print("	ErrorLog /var/log/apache2/error.log")

print("	ServerName {}{}".format("" if config['www'] else "www.", domain))
print("	Redirect permanent / http{}://{}{}/".format("s" if config['ssl']['active'] else "", "www." if config['www'] else "", domain))
print("</VirtualHost>")

#### primary vhost
print("<VirtualHost *:{}>".format("8443" if config['ssl']['active'] else "8080"))
print("	ServerName {}{}".format("www." if config['www'] else "", domain))
print("	DocumentRoot {}".format(config['path']))
print("	")

# options
# indexes?
if config['allowIndexes']:
	print("	Options +Indexes")

print("	")



# logging
print("	CustomLog {}access.log vhost_combined".format(config['logPath']))
print("	ErrorLog {}error.log".format(config['logPath']))
print("	CustomLog /var/log/apache2/access.log vhost_combined")
print("	ErrorLog /var/log/apache2/error.log")

print("	<Directory />")

# overrides?
if config['allowOverrides']:
	print("		AllowOverride All")


print("	</Directory>")
print("	")
print("	AssignUserId {} {}".format(user, user))
print("</VirtualHost>")

# print(config['path'])
