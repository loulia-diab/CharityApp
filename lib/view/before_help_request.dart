import 'package:charity_project/app_colors.dart';
import 'package:charity_project/view/app_text_style.dart';
import 'package:charity_project/view/background.dart';
import 'package:charity_project/view/request_help_page.dart';
import 'package:flutter/material.dart';

class BeforeHelpRequest extends StatelessWidget {
  const BeforeHelpRequest({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      body: BackgroundWrapper(child: Column(
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [AppBar(
          backgroundColor: AppColors.white,
        ),
          Image.asset('assets/images/help3.png',height: 250,),
          SizedBox(height: 30,),
          Text('Do You Need Help?',style: AppTextStyle.a,),
          SizedBox(height: 20,),
          Center(
            child: Text("We are here to support you.Whether you are facing financialhardshipor an urgent\nsituation,you can submit\na help request and our team\nwill review your case as soon as possible.",
            style:AppTextStyle.helpReq ,
            textAlign: TextAlign.center,),
          ),
      SizedBox(height: 60,),
      ElevatedButton(onPressed: (){
        Navigator.push(context, MaterialPageRoute(builder: (context)=> RequestHelpPage()));
      }, child: Text('Request Help'),
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