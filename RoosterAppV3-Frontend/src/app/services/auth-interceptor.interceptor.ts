import { HttpInterceptorFn } from '@angular/common/http';
import { jwtDecode } from 'jwt-decode';
import { environment } from '../../environments/environment.development';
import { EMPTY } from 'rxjs';
import { inject } from '@angular/core';
import { Router } from '@angular/router';

export const authInterceptorInterceptor: HttpInterceptorFn = (req, next) => {

  const publicRoutes = [
    environment.apiurl + "/token"
  ];

  // inject services
  const router = inject(Router);

  if (!publicRoutes.includes(req.url)) {
    // not a public route

    // get the token
    const token = localStorage.getItem('token');

    // if token isnt in localstorage redirect to login
    if (token == null) {
      router.navigateByUrl('');
      return EMPTY;  
    }

    // check if token is still valid
    const decoded = jwtDecode(String(token));
    const currentTime = Math.floor(Date.now() / 1000); // get the current timestamp in seconds
    // check if the expiration timestamp is higher than the current timestamp
    if (decoded.exp != null && decoded.exp > currentTime) {
      // token is valid - add auth header and return the request
      req = req.clone({
        headers: req.headers.set(
          'Authorization', 'Bearer ' + token
        )
      });

      return next(req);
    }
    else {
      // token invalid - redirect user to login
      router.navigateByUrl('');
      return EMPTY;
    }
  }
  else {
    // public route - return request
    return next(req);
  }
};
