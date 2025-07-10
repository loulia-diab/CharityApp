import 'package:charity_project/app_colors.dart';
import 'package:charity_project/view/app_text_style.dart';
import 'package:charity_project/view/background.dart';
import 'package:charity_project/view/request_help_page.dart';
import 'package:flutter/material.dart';

class BeforeGift extends StatelessWidget {
  const BeforeGift({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      
      body: BackgroundWrapper(child: Column(
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [AppBar(
          backgroundColor: AppColors.background,
        ),
          Image.asset('assets/images/mv.png',height: 340,),
          SizedBox(height: 10,),
          Text('Do You Need Help?',style: AppTextStyle.a,),
          SizedBox(height: 20,),
          Center(
            child: Text("We are here to support you.Whether you are facing financialhardshipor an urgent\nsituation,you can submit\na help request and our team\nwill review your case as soon as possible.",
            style:AppTextStyle.helpReq ,
            textAlign: TextAlign.center,),
          ),
      SizedBox(height: 60,),
      ElevatedButton(onPressed: (){
        // Navigator.push(context, MaterialPageRoute(builder: (context)=> RequestHelpPage()));
      }, child: Text('Send Gift'),
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