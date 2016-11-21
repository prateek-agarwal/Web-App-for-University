import { Component } from '@angular/core';

import { NavController } from 'ionic-angular';

@Component({
  selector: 'page-page3',
  templateUrl: 'page3.html'
})
export class Page3 {

  constructor(public navCtrl: NavController) {
    
  }
  
  // public courses = {
  //   coursename1:'Compiler Design',
  //   atd1:'92.5%',
  //   coursename2:'Microprocessor and Microcontroller',
  //   atd2:'95.8%',
  //   coursename3:'Basics of Management',
  //   atd3:'91%',
  //   coursename4:'Theory of Computation',
  //   atd4:'100%',
  //   coursename5:'Computer Networking',
  //   atd5:'87.5%',
  //   coursename6:'Software Engineering',
  //   atd6:'94.5%'
  // }

  lists = [
    { id : "Compiler Design",
      bgcolor: '#fb9667',
      nclass:'92.5%'},
    
    {id : "Microprocessor and Microcontroller",
      bgcolor: 'mediumpurple',
      nclass:'95.8%'},
    
    {id : "Basics of Management",
      bgcolor: 'orange',
      nclass:'91%'},
    
    {id : "Theory of Computation",
      bgcolor: 'mediumturquoise',
      nclass:'100%'},
    
    {id : "Computer Networking",
      bgcolor: ' lightcoral',
      nclass:'87.5%'},
 
    {id : "Software Engineering",
      bgcolor: 'yellowgreen',
      nclass:'94.5%'}
    
  ];
  
}