#include <stdlib.h>
#include <string.h>

void get_first_address(char*, int, char*);

int main() {

	/*
	   HTTPS fix

	   If the request headers (which have been sanitized by the
	   reverse proxy talking to Apache) indicate that the request
	   originated from HTTPS, then set SERVER_PORT to 443 and
	   HTTPS to on (otherwise, 80 and unset)
	   */
	char* header_forwarded_proto = getenv("HTTP_X_FORWARDED_PROTO");

	if (header_forwarded_proto) {
		if (strnlen(header_forwarded_proto, 5) == 5 && strncmp(header_forwarded_proto, "https", 5) == 0) {
			setenv("SERVER_PORT", "443", 1);
			setenv("HTTPS", "on", 1);
		} else {
			setenv("SERVER_PORT", "80", 1);
			unsetenv("HTTPS");
		}
	}

	/*
	   Client address fix

	   For convenience, we set the first value in the
	   X-Forwarded-For header to the client's IP (we can trust the
	   value only since we've already sanitized it)
	   */
	char* header_forwarded_for = getenv("HTTP_X_FORWARDED_FOR");

	if (header_forwarded_for) { // if the header is set
		char first_address[16];
		get_first_address(first_address, 16, header_forwarded_for); /* get the part before the first comma */

		if (strnlen(first_address, 15) > 0) { /* this should always be true */
			setenv("REMOTE_ADDR", first_address, 1);
		}
	}

	/*
	   Header cleanup

	   Remove some headers that the scripts don't ever need to be
	   aware of.
	   */
	unsetenv("HTTP_X_FORWARDED_FOR");
	unsetenv("HTTP_X_FORWARDED_PROTO");

	/* call php-cgi with our corrected environment variables */
	system("/usr/bin/php-cgi");

	return 0;
}

void get_first_address(char* first_address, int len, char* header) {
	first_address[0] = '\0'; /* make the address essentially empty */

	int i;

	for (i = 0; i < strnlen(header, (len - 1)); i ++) {
		char c = *(header + i);

		if (c == ',' || c == ' ') {
			break;
		}

		first_address[i] = c;
	}
}
