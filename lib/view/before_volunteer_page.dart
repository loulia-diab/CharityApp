import 'package:charity_project/app_colors.dart';
import 'package:charity_project/view/app_text_style.dart';
import 'package:charity_project/view/background.dart';
import 'package:charity_project/view/inKind_donaition_request.dart';
import 'package:charity_project/view/request_help_page.dart';
import 'package:charity_project/view/volunteer_request_page.dart';
import 'package:flutter/material.dart';

class BeforeVolunteerPage extends StatelessWidget {
  const BeforeVolunteerPage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
          backgroundColor: AppColors.background,
        ),
      body: BackgroundWrapper(
        child: Column(
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          Image.asset('assets/images/volunteer.png',height: 300,),
          SizedBox(height: 10,),
          Text('Your Time and Skills Can Change Lives',textAlign: TextAlign.center,style: AppTextStyle.a,),
          SizedBox(height: 20,),
          Center(
            
            child: Text(
              'Your time and skills can change lives.\n'
        'There are people who need your help —\n'
        'your kindness, your time, your effort.\n'
        'Join us as a volunteer and make a real impact\n'
        'in your community.\n'
        'Whether it’s organizing events,\n'
        'helping with distribution,\n'
        'or simply offering a helping hand —\n'
        'every effort matters.\n'
        'Apply now and become a part of something meaningful.'
              ,
            style:AppTextStyle.helpReq ,
            textAlign: TextAlign.center,),
          ),
      SizedBox(height: 20,),
      ElevatedButton(onPressed: (){
        Navigator.push(context, MaterialPageRoute(builder: (context)=> VolunteerRequestPage()));
      }, child: Text('New Request'),
      style: ElevatedButton.styleFrom(
        backgroundColor: AppColors.primary,
        fixedSize: Size(200, 50),
        foregroundColor: AppColors.white
      ),
      )
      
          ]
        
      ),),
    );
   
  }
}