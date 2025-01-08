import { Component } from '@angular/core';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { HttpHelperService } from '../services/http-helper.service';
import { Router } from '@angular/router';
import { NgbAlertModule } from '@ng-bootstrap/ng-bootstrap';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [ReactiveFormsModule, NgbAlertModule, CommonModule],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css'
})
export class LoginComponent {

  constructor(private http: HttpHelperService,
    private router: Router
  ) {}

  loginForm = new FormGroup({
    email: new FormControl('', Validators.required),
    password: new FormControl('', Validators.required)
  });

  error: boolean;
  errorText: string;

  login(): void {
    this.http.post('/token', this.loginForm.value).subscribe({
      next: (resp) => {
        const response:any = resp.body;
        localStorage.setItem('token', response.token);
        this.router.navigateByUrl('/dashboard');
      },
      error: (error) => {
        this.error = true;
        if (error.status == 400) {
          this.errorText = 'Inlognaam en/of wachtwoord onjuist.'
        }
        else if (error.status == 403) {
          this.errorText = "Account is geblokkeerd. Raadpleeg een beheerder.";
        }
        else {
          this.errorText = 'Fout opgetreden in API: ' + error.message
        }
        console.log(error)
      }
    });
  }

}
