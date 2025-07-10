import 'package:charity_project/app_colors.dart';
import 'package:charity_project/view/Kaffarat_and_Sadaqah_view.dart';
import 'package:charity_project/view/background.dart';
import 'package:charity_project/view/donate_campaigns_page.dart';
import 'package:flutter/material.dart';

import 'package:charity_project/view/app_text_style.dart';
List <Map<String,String>> KaffaratandSadaqah =[
  {"name":"Feeding A Poor",
  "image":"assets/images/feed.jpg"},
  {"name":"Clothing A Poor",
  "image":"assets/images/clothing.jpg"},
  {"name":"Expiation for Breaking an Oath",
  "image":"assets/images/oath.jpg"},
  {"name":"Fulfillment of a Vow",
  "image":"assets/images/vow.jpg"},
  {"name":"Sacrifice",
  "image":"assets/images/sacrifice.jpg"},
  {"name":"Aqiqah",
  "image":"assets/images/aqiaqah.jpg"},
];
class OneKaffaratAndSadaqahPage extends StatelessWidget {
  const OneKaffaratAndSadaqahPage({super.key,required this.title,required this.image,required this.cost});
final String title;
final String image;
final String cost;
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        backgroundColor: AppColors.white,
    
      ),
      body: BackgroundWrapper(
        child: SizedBox(height: 800,
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
                    image: DecorationImage(image: AssetImage(image),fit:BoxFit.cover )
                    
                  ),
                ),
            
              ),
              Text(title,style: AppTextStyle.a,),
              Divider(),
              


              SizedBox(height: 10,),

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
                            Text("Cost for one ",style: AppTextStyle.helpReq,),
                            Text(cost , style: AppTextStyle.a,),
                            
                          ],
                        ),
                      ),
                      SizedBox(width: 90,),
                      Image.asset("assets/images/ass.png",height: 90,)
                      
                      ],
                    ),
                  ),
                ),
              ),
                              
              Center(child: Text("Set Donaition Amount :",style: AppTextStyle.helpReq,)),
              SizedBox(height: 20,),
              Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  InkWell(
                    onTap: (){},
                    child: Container(
                      height: 40,width: 40,
                      decoration: BoxDecoration(
                        border: Border.all(color: AppColors.black,width: 2),
                        borderRadius: BorderRadius.circular(30)
                        
                      ),
                      child: Icon(Icons.add,color: AppColors.secondary,),
                    ),
                  ),
                  SizedBox(width: 10,),
                  Text("0",style: TextStyle(
    color: AppColors.primary,
    fontWeight: FontWeight.w600,
    fontSize: 22,

  ),),
                  SizedBox(width: 10,),
                  InkWell(
                    onTap: (){},
                    child: Container(
                      height: 40,width: 40,
                      decoration: BoxDecoration(
                        border: Border.all(color: AppColors.black,width: 2),
                        borderRadius: BorderRadius.circular(30)
                        
                      ),
                      child: Icon(Icons.remove,color: AppColors.secondary,),
                    ),
                  ),
                ],
              ),
              SizedBox(height: 10,),
Divider(),
              Padding(
                padding: const EdgeInsets.all(8.0),
                child: Row(mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Padding(
                                  padding:
                                      const EdgeInsets.only(left: 20, right: 20),
                                  child: Text(
                                    'Total Amount :',
                                    style: TextStyle(
                                      color: AppColors.black,
                                      fontSize: 20,
                                      fontWeight: FontWeight.w700,
                                    ),
                                  ),
                                ),
                                Text(
                                   cost,
                                  style: TextStyle(
                                    color: AppColors.primary,
                                    fontSize: 20,
                                    fontWeight: FontWeight.w700,
                                  ),
                                )
                              ],
                            ),
              ),
SizedBox(height: 30,),
 Column(
                            
                            
                            children: [
                              Padding(
                                padding: const EdgeInsets.symmetric(horizontal: 50),
                                child: ElevatedButton(
                                  onPressed: 
                                      () {
                                          
                                        }
                                      ,
                                  child: Row(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [
                                      Icon(Icons.payment,
                                          color: AppColors.white, size: 30),
                                      SizedBox(width: 10),
                                      Text("Pay Now",
                                          style: TextStyle(fontSize: 16)),
                                    ],
                                  ),
                                  style: ElevatedButton.styleFrom(
                                    backgroundColor: AppColors.primary,
                                    foregroundColor: AppColors.white,
                                    fixedSize: Size(250, 50),
                                  ),
                                ),
                              ),
                              SizedBox(height: 20),
                              Padding(
                                padding: const EdgeInsets.only(bottom: 20),
                                child: ElevatedButton(
                                  onPressed:
                                       () {},
                                      
                                  child: Row(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [
                                      Image.asset('assets/images/fund.png',
                                          color: AppColors.primary, height: 30),
                                      SizedBox(width: 10),
                                      Text("Add to Cart",
                                          style: TextStyle(fontSize: 16)),
                                    ],
                                  ),
                                  style: ElevatedButton.styleFrom(
                                    shape: RoundedRectangleBorder(
                                      side: BorderSide(
                                          color: AppColors.primary, width: 2),
                                      borderRadius: BorderRadius.circular(25),
                                    ),
                                    backgroundColor: AppColors.white,
                                    foregroundColor: AppColors.primary,
                                    fixedSize: Size(250, 50),
                                  ),
                                ),
                              ),
                            ],
                          ),


                             
                              
            ],
            ),
          ),
        ),
     ) ));
  }
}