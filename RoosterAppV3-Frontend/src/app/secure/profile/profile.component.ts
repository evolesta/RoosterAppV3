import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { NgbAlertModule, NgbNav, NgbNavModule } from '@ng-bootstrap/ng-bootstrap';
import { HttpHelperService } from '../../services/http-helper.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-profile',
  standalone: true,
  imports: [NgbNavModule, ReactiveFormsModule, NgbAlertModule, CommonModule],
  templateUrl: './profile.component.html',
  styleUrl: './profile.component.css'
})
export class ProfileComponent implements OnInit {

  profileForm = new FormGroup({
    name: new FormControl('', Validators.required),
    workHours: new FormControl('', Validators.required)
  });

  passwordForm = new FormGroup({
    oldPassword: new FormControl('', Validators.required),
    newPassword: new FormControl('', Validators.required),
    newPassword2: new FormControl('', Validators.required)
  });

  alert: boolean = false;
  alertType: string = 'success';
  alertText: string = '';

  constructor(private http: HttpHelperService) {}

  ngOnInit(): void {
    this.getProfile();
  }

  getProfile(): void {
    this.http.get('/profile').subscribe(resp =>
      {
        this.profileForm.patchValue(resp.body);
      }
    )
  }

  submitProfile(): void {
    this.http.put('/profile', this.profileForm.value).subscribe(resp => {
      this.alert = true;
      this.alertText = 'Instellingen zijn succesvol opgeslagen.';
      this.getProfile();
    });
  }

  submitPassword(): void {
    this.http.put('/profile/password', this.passwordForm.value).subscribe({
      next(resp) {
          this.alert = true;
          this.alertText = 'Wachtwoord is succesvol gewijzigd.';
      },
      error(error) {
        this.alert = true;
        this.alertText = "Fout: " + error.error;
        this.alertType = "danger";
      }
    })  
  }

}
