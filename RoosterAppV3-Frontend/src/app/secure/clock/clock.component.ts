import { DatePipe } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';

@Component({
  selector: 'app-clock',
  standalone: true,
  imports: [ReactiveFormsModule],
  templateUrl: './clock.component.html',
  styleUrl: './clock.component.css'
})
export class ClockComponent implements OnInit {

  clockForm = new FormGroup({
    date: new FormControl('', Validators.required),
    start: new FormControl('', Validators.required),
    end: new FormControl()
  });

  ngOnInit(): void {
    this.getCurrentDate();
  }

  private getCurrentDate(): void {
    const pipe = new DatePipe('en-US');
    this.clockForm.controls.date.setValue(pipe.transform(Date.now(), 'yyyy-MM-dd'));
    this.clockForm.controls.start.setValue(pipe.transform(Date.now(), 'HH:mm'));
  }
}
