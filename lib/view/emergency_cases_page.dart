import 'package:flutter/material.dart';
import 'package:charity_project/app_colors.dart';
import 'package:charity_project/view/app_text_style.dart';
import 'package:charity_project/view/background.dart';
import 'package:charity_project/view/donaition_category_view_page.dart';
import 'package:charity_project/view/one_HumanitarianCases_view_page.dart';
import 'package:charity_project/view/one_campaign_page.dart';
import 'package:flutter/material.dart';
import 'package:percent_indicator/linear_percent_indicator.dart';
class EmergencyCasesPage extends StatelessWidget {
  const EmergencyCasesPage({super.key});

  @override
  Widget build(BuildContext context) {
    return  Padding(
            padding: const EdgeInsets.symmetric(horizontal: 10,vertical: 5),
            child: Container(
              height: 180,width: 180,
              
              child: Card(
                elevation: 10,
                color: AppColors.white,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(10)
                ),
                child: Column(
                  children: [
                    Container(
                      height: 100,
                      width: 180,
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(10),
                        image: DecorationImage(image: AssetImage("assets/images/case.jpg",),fit: BoxFit.cover)
                      ),
                    ),
                    Padding(
                      padding: const EdgeInsets.only(top:4,left: 5,right: 5),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text("Emergency Surgery ",style: AppTextStyle.b,),
                          
                          SizedBox(height: 0,),
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            
                            children: [
                              Padding(
                                padding: const EdgeInsets.only(left: 120),
                                child: Text("90%",style: AppTextStyle.c,),
                              ),
                              SizedBox(width: 250,
                                child: LinearPercentIndicator(
                                  maskFilter: MaskFilter.blur(BlurStyle.solid, 3),
                                  linearGradient: LinearGradient(colors: [AppColors.primary,AppColors.teal]),
                                  barRadius: Radius.circular(10),
                                  curve: Curves.easeInOut,
                                  clipLinearGradient: true,
                                  lineHeight: 10,
                                  // progressColor: AppColors.primary,
                                  percent: progress,
                                  animation: true,
                                  animationDuration: 1000,
                                  
                                ),
                              ),
                              Padding(
                                padding: const EdgeInsets.only(left: 10),
                                child: Row(
                                  children: [
                                 Text("\$${raisedamount.toInt()} / \$${goalamount.toInt()}", style: AppTextStyle.c)
                                
                                ],),
                              ),
                            ],
                          ),
                         
                        ],
                        
                      ),
                    )
                  ],
                ),
              ),
            ),
          );;
  }
}