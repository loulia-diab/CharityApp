import 'package:charity_project/app_colors.dart';
import 'package:charity_project/view/app_text_style.dart';
import 'package:charity_project/view/background.dart';
import 'package:charity_project/view/donaition_category_view_page.dart';
import 'package:charity_project/view/donate_campaigns_page.dart';
import 'package:flutter/material.dart';
import 'package:percent_indicator/linear_percent_indicator.dart';
class OneSponsorshipsPage extends StatefulWidget {
  const OneSponsorshipsPage({super.key});

  @override
  State<OneSponsorshipsPage> createState() => _OneSponsorshipsPageState();
}

class _OneSponsorshipsPageState extends State<OneSponsorshipsPage> {
  @override
  Widget build(BuildContext context) {
   final progress = (raisedamount / goalamount).clamp(0.0, 1.0);
    return Scaffold(
       backgroundColor: AppColors.background,
      appBar: AppBar(
        backgroundColor: AppColors.white,
      ),
      body: BackgroundWrapper(child: 
      SizedBox(height: 800,
        child: SingleChildScrollView(
          child: Padding(
            padding: const EdgeInsets.only(right: 20,left: 20),
            child: Column(crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Padding(
                padding: const EdgeInsets.all(8.0),
                child: Container(
                  height: 200,width: double.infinity,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(20),
                    image: DecorationImage(image: AssetImage("assets/images/sponser.jpg"),fit:BoxFit.cover )
                  ),
                ),
            
              ),
              Text('Nour',style: AppTextStyle.a,),
              Divider(),
              SizedBox(height: 10,),
              Text('About Campaign ',style: AppTextStyle.helpReq,),
              SizedBox(height: 10,),
              SizedBox(height: 260,width: double.infinity,
                child: Text("Nour lost her father at a young age and now lives with her widowed mother in a poor rural area.Her mother struggles to provide even the most basic necessities like food, school supplies, and medical care.Through your monthly sponsorship, you can ensure Nour continues her education, receives proper healthcare, and grows up with hope and dignity.Sponsorship covers food, clothing, education expenses, and emotional support.Be the reason Nour smiles again. Your kindness can change her life.",
                style: AppTextStyle.d))


              ,SizedBox(height: 10,),
              //  Column(
              //                   crossAxisAlignment: CrossAxisAlignment.start,
              //                   children: [
              //                     Padding(
              //                       padding: const EdgeInsets.only(left: 10,),
              //                       child: Row(
              //                         children: [
              //                        Text("\$${raisedamount.toInt()} ",style: AppTextStyle.a,),
              //                         Text("of \$${goalamount.toInt()} ",style: AppTextStyle.agray,),
              //                         SizedBox(width: 120,),
              //                         Text("90% ",style: AppTextStyle.a,),
              //                       ],),
              //                     ),
              //                     Padding(
              //                       padding: const EdgeInsets.all(8.0),
              //                       child: SizedBox(width: 800,
              //                         child: LinearPercentIndicator(
              //                           maskFilter: MaskFilter.blur(BlurStyle.solid, 3),
              //                           linearGradient: LinearGradient(colors: [AppColors.primary,AppColors.teal]),
              //                           barRadius: Radius.circular(10),
              //                           curve: Curves.easeInOut,
              //                           clipLinearGradient: true,
              //                           lineHeight: 15,
              //                           // progressColor: AppColors.primary,
              //                           percent: progress,
              //                           animation: true,
              //                           animationDuration: 1000,
                                        
              //                         ),
              //                       ),
              //                     ),
              //                     Padding(
              //                       padding: const EdgeInsets.only(left: 200),
              //                       child: Text("\$ 200 Remaining",style: AppTextStyle.d,),
              //                     ),
                                  
                                  
              //                   ],
              //                 ),

              Padding(
                padding: const EdgeInsets.only(bottom: 20),
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
                            Text("Monthly Amount",style: AppTextStyle.helpReq,)
                          ],
                        ),
                      ),
                      SizedBox(width: 60,),
                      Image.asset("assets/images/ass.png",height: 90,)
                      
                      ],
                    ),
                  ),
                ),
              ),
                              Divider(),
                              Text("More details :",style: AppTextStyle.helpReq,) ,
                              Row(mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                                children: [
                                  Padding(
                                    padding: const EdgeInsets.only(top: 25),
                                    child: Column(
                                      children: [
                                        Icon(Icons.border_color,color: AppColors.primary,size: 32,),
                                        SizedBox(height: 10,),
                                         Text("Type",style: AppTextStyle.d,textAlign: TextAlign.center,),
                                        SizedBox(height: 10,),
                                        Text("Orphan",style: AppTextStyle.helpReq,)
                                      ],
                                    ),
                                  )
                                  ,SizedBox(height: 100,
                                    child: VerticalDivider()),
                                Padding(
                                    padding: const EdgeInsets.only(top: 25),
                                    child: Column(
                                      children: [
                                        Icon(Icons.person,color: AppColors.primary,size: 30,),
                                        SizedBox(height: 10,),
                                         Text("Gender",style: AppTextStyle.d,textAlign: TextAlign.center,),
                                        SizedBox(height: 10,),
                                        Text("Female",style: AppTextStyle.helpReq,)
                                      ],
                                    ),
                                  ),
                                  SizedBox(height: 100,
                                    child: VerticalDivider()),
                                  Padding(
                                    padding: const EdgeInsets.only(top: 25),
                                    child: Column(
                                      children: [
                                        Icon(Icons.date_range,color: AppColors.primary,size: 32,),
                                        SizedBox(height: 10,),
                                         Text("BirthDate",style: AppTextStyle.d,textAlign: TextAlign.center,),
                                        SizedBox(height: 10,),
                                        Text("20/1/2005",style: AppTextStyle.helpReq,)
                                      ],
                                    ),
                                  )
          
          
          
                                ],
                              ),


                              Padding(
               padding:  EdgeInsets.only(right: 20,top: 30,bottom: 20),
               child: Center(
                 child: ElevatedButton(onPressed: (){
                  
                  Navigator.push(context, MaterialPageRoute(builder: (context)=> DonateCampaignsPage()));
                 }, child: Text('Donate'),
                 style: ElevatedButton.styleFrom(
                   backgroundColor: AppColors.secondary,
                   fixedSize: Size(200, 50),
                   foregroundColor: AppColors.white
                 ),
                 ),
               ),
             )
                              
            ],
            ),
          ),
        ),
      )),
    );
  }
}