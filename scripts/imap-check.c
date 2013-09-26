/*
 * Check whether a given email address and password are valid
 * on the mercury IMAP server.
 *
 * Email and password should be piped into the program on separate
 * lines
 *
 * Example usage:
 *     echo "chris@browseright.org\npassword" | imap-check
 *
 * Exits with status code 0 on success, non-0 on failure.
 *
 * Intended for use with Apache's mod_auth_external and uses
 * sample code from that project
 *
 * https://code.google.com/p/mod-auth-external/
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <curl/curl.h>

int main(void)
{
	char user[100], password[100], *p;

	if (fgets(user, sizeof(user), stdin) == NULL) exit(2);
	if ((p= strchr(user, '\n')) == NULL) exit(4);
	*p= '\0';

	if (fgets(password, sizeof(password), stdin) == NULL) exit(3);
	if ((p= strchr(password, '\n')) == NULL) exit(5);
	*p= '\0';

	if (check_password(user, password) == 0) {
		printf("login successful");
		exit(0);
	} else {
		printf("bad user/password");
		exit(1);
	}
}

int check_password(char user[100], char password[100]) {
	CURL *curl;
	CURLcode res = CURLE_OK;

	curl = curl_easy_init();

	if (curl) {
		char* credentials = malloc(snprintf(NULL, 0, "%s:%s", user, password) + 1);
		sprintf(credentials, "%s:%s", user, password);
		
		FILE* f = fopen("/dev/null", "wb");

		curl_easy_setopt(curl, CURLOPT_USERPWD, credentials);
		curl_easy_setopt(curl, CURLOPT_URL, "imaps://mail.mercury.techxonline.net:993/INBOX");
		curl_easy_setopt(curl, CURLOPT_WRITEDATA, f);
		
		res = curl_easy_perform(curl);
		curl_easy_cleanup(curl);

		return res;
	}

	return 1;
}
