import 'dart:math';

import 'package:charity_project/app_colors.dart';
import 'package:charity_project/view/app_text_style.dart';
import 'package:charity_project/view/background.dart';
import 'package:charity_project/view/one_Sponsorships_page.dart';
import 'package:charity_project/view/one_kaffarat_and_sadaqah_page.dart';
import 'package:flutter/material.dart';
List <Map<String,String>> KaffaratandSadaqah =[
  {"name":"Feeding A Poor",
  "image":"assets/images/feed.jpg",
  "cost":"\$ 25"},
  {"name":"Clothing A Poor",
  "image":"assets/images/clothing.jpg",
  "cost":"\$ 55"},
  {"name":"Expiation for Breaking an Oath",
  "image":"assets/images/oath.jpg",
  "cost":"\$ 50"},
  {"name":"Fulfillment of a Vow",
  "image":"assets/images/vow.jpg",
  "cost":"\$ 45"},
  {"name":"Sacrifice",
  "image":"assets/images/sacrifice.jpg",
  "cost":"\$ 250"},
  {"name":"Aqiqah",
  "image":"assets/images/aqiaqah.jpg",
  "cost":"\$ 250"},
];
class KaffaratAndSadaqahView extends StatefulWidget {
  const KaffaratAndSadaqahView({super.key});

  @override
  State<KaffaratAndSadaqahView> createState() => _KaffaratAndSadaqahViewState();
}

class _KaffaratAndSadaqahViewState extends State<KaffaratAndSadaqahView> {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        backgroundColor: AppColors.white,
        title: Text("Kaffarat and Sadaqah",style: AppTextStyle.a,),
      ),
      body: BackgroundWrapper(child: Column(
        children: [
          Expanded(
            child: ListView.builder(itemCount: KaffaratandSadaqah.length,
              itemBuilder: (context,index){
                return InkWell(onTap: (){},
                  child: Padding(
                    padding: const EdgeInsets.all(8.0),
                    child: AnimatedContainer(
                      duration: Duration(microseconds: 10000),
                     curve:  Curves.easeInOut,
                     
                      height: 130,
                    width: double.infinity,
                    child: Card(
                       elevation: 10,
                      color: AppColors.white,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(10)
                      ),
                      child: Row(
                        children: [
                          Padding(
                            padding: const EdgeInsets.only(left: 20,right: 20),
                            child: Stack(children: [
                                Container(
                                height: 100,
                                width: 100,
                                decoration: BoxDecoration(
                                  borderRadius: BorderRadius.circular(10),
                                  image: DecorationImage(image: AssetImage(KaffaratandSadaqah[index]["image"]!),fit: BoxFit.cover)
                                ),
                              ),

Container(height: 100,
                                width: 100,
                            decoration: BoxDecoration(
                               borderRadius: BorderRadius.circular(10),
                              gradient: LinearGradient(
                                colors: [AppColors.primary.withOpacity(0.5), Colors.transparent],
                                begin: Alignment.bottomCenter,
                                end: Alignment.topCenter,
                              ),
                            ),
                          ),
                               
                            ]
                             
                            ),
                          ),
                          Column(crossAxisAlignment: CrossAxisAlignment.start,
                          mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                            Column(
                              children: [
                                SizedBox(height: 50,width: 150,
                                  child: Text(KaffaratandSadaqah[index]['name']!,style: TextStyle(
    color: AppColors.primary,
    fontWeight: FontWeight.w700,
    fontSize: 17,

  ),)),
                              ],
                            ),

Padding(
                                 padding: const EdgeInsets.only(top:0),
                                 child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    
                                    Row(
                                      children: [
                                        Image.asset("assets/images/as.png",height: 20,),
                                        SizedBox(width: 6,),
                                        Text("\$ 200",style: AppTextStyle.helpReq,)
                                      ],
                                    )
                                  ],
                                                               ),
                               ),

                                 Row(crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                               
                              Padding(
                                padding: EdgeInsets.only(left: 90),
                                child: ElevatedButton(onPressed: (){
                                  Navigator.push(context, MaterialPageRoute(builder: (context)=>OneKaffaratAndSadaqahPage(
                                title: KaffaratandSadaqah[index]["name"]!,
                                image: KaffaratandSadaqah[index]["image"]!,
                                cost :KaffaratandSadaqah[index]["cost"]!
                                  )));
                                }, child: Text("Donate"),
                                style:ElevatedButton.styleFrom(
                                  backgroundColor: AppColors.secondary,
                                  foregroundColor: AppColors.white,
                                  fixedSize: Size(100, 30)
                                ) ,),
                              ),
                            ],
                          )
                          ],)
                        ],
                      ),
                    ),
                    ),
                  ),
                );
              }),
          )
        ],
      )),
    );
  }
}