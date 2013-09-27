#!/bin/bash
# adds or updates users for the mercury server
OP_USER=$1

# create the user if they don't already exist
adduser --disabled-password --gecos "" $OP_USER

# create their home directory
mkdir -p /home/$OP_USER/$OP_USER/
usermod -d /home/$OP_USER/$OP_USER $OP_USER

# add some SSH keys
if [ ! -f "/home/$OP_USER/$OP_USER/.ssh/authorized_keys" ]; then
	echo "Adding SSH key..."
	mkdir -p /home/$OP_USER/$OP_USER/.ssh/
	echo "ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAACAQDFvfV0FDPVZ0nwXHxUiJMqtQz+XhD1+gZLBKVmocXGIY8GhTj12NhsFW7PBcmu0V+okKgJuWoOOeEZLHFsCfkwbH1scmH28g1o1EIbnM7gxnmRq8x1s62uQBiMyT6jGDN+ZMLn8sZxclhRqYLpdO5zqIT5omnPnJEn2HcoZkr/5iNn6vWZfNLndbGuuiVCu9xNSc2PHCGFW4XY39NJToLz5qt37/BcLyJMdZE9dXYbqJEBfGKHhasV8biXfC5N3mnTlKly6V/52UCEveLpIWBUe1UDbGHpeWcezT1S5Yw7lIHKHK/MHGdBmGrdeQzDZAD2IRN+/BR+jaCjkgw03wKXHfmWoSE77fCLj952JGHPaIYLqeD89hIAUEJ724b80tCrCjVcwYersn7TqwvNFKXwqrfc4Ze1ko8WCsChErvfOgJLiTRu0MO6NEfLG09h4i15md4IFZtzI5D+QuJ0A/cW828ux2ibGKAuIN79yQJphN/MESiXfwitkqzNUORSfeXOlres5sSpLeQOsl3QBULMgm53aiNYJzT/7I9fbT6ZVZt6jNw4dO7n66EYl62JGQ+AILOOeSI3TvGhGnmIWaFvb3v7eWjAHYTManC/FORHZlf0kPNQP3fl4aat5/NQvTbIFMWoPkogOL4ZxuM85zibDyBm/5nPtGklrcGX20buWw== chris@techxonline.net" \
		> /home/$OP_USER/$OP_USER/.ssh/authorized_keys
else
	echo "Not adding SSH key (authorized_keys exists)"
fi

# home directory permissions
chown root:$OP_USER /home/$OP_USER/
chmod 750 /home/$OP_USER/
chown $OP_USER:$OP_USER /home/$OP_USER/$OP_USER/ /home/$OP_USER/$OP_USER/.ssh/
chmod 700 /home/$OP_USER/$OP_USER/.ssh/

# add to the right groups
usermod -a -G pubuser $OP_USER
usermod -a -G pubuser-shell $OP_USER
