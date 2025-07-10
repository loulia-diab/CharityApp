import 'package:charity_project/app_colors.dart';
import 'package:charity_project/view/app_text_style.dart';
import 'package:charity_project/view/background.dart';
import 'package:charity_project/view/donate_page.dart';
import 'package:flutter/material.dart';
import 'package:percent_indicator/flutter_percent_indicator.dart';

class DonateCampaignsPage extends StatefulWidget {
  const DonateCampaignsPage({super.key});

  @override
  State<DonateCampaignsPage> createState() => _DonateCampaignsPageState();
}

class _DonateCampaignsPageState extends State<DonateCampaignsPage> {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: Text("Donation for a campaign",style: AppTextStyle.a,),
        backgroundColor: AppColors.white,
      ),

      body: 
      SingleChildScrollView(
        child: BackgroundWrapper(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
             
              Padding(
                padding: const EdgeInsets.only(bottom: 20,top: 40),
                child: Center(
                  child: Container(
                    width: 350,height: 100,
                    decoration: BoxDecoration(
                      color: AppColors.primary.withOpacity(0.4),
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(color: AppColors.primary)
                    ),
                    child: Row(
                      children: [Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 20,vertical: 20),
                        child: Column(crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text("\$ 200" , style: AppTextStyle.a,),
                            Text("Remaining amount",style: AppTextStyle.helpReq,)
                          ],
                        ),
                      ),
                      SizedBox(width: 60,),
                      Image.asset("assets/images/donate.png",height: 70,)
                      
                      ],
                    ),
                  ),
                ),
              ),
              //  Center(
              //    child: CircularPercentIndicator(radius: 100,
                  
              //                             linearGradient: LinearGradient(colors: [AppColors.primary,AppColors.teal]),
                                          
              //                             curve: Curves.easeInOut,
              //                             lineWidth: 20,
                                          
              //                             // progressColor: AppColors.primary,
              //                             percent: 0.9,
              //                             animation: true,
              //                             animationDuration: 1000,
              //    center: Text("40%"),),
              //  ),
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 20,vertical: 20),
                child: Text("Set Donation Amount",style: AppTextStyle.a,),
              ),
              
              DonateWidget()
            ],
          ),
        ),
      ),
    );
  }
}