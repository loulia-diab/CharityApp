import 'package:charity_project/app_colors.dart';
import 'package:charity_project/view/background.dart';
import 'package:flutter/material.dart';

class CharityFundPage extends StatelessWidget {
  const CharityFundPage({super.key});






  @override
  Widget build(BuildContext context) {
  
    return Scaffold(
      backgroundColor: AppColors.background,
      body: BackgroundWrapper(
        child: Column(
          children: [
            AppBar(
              
              backgroundColor: AppColors.white,
              // elevation: 5,
              // shadowColor: AppColors.unselected,
              title: Text('charity Fund',style: TextStyle(
                color: AppColors.primary,fontWeight: FontWeight.w600
              ),),
            ),
           SizedBox(height: 530,
             child: ListView.builder(itemCount: 10,
             scrollDirection: Axis.vertical,
              itemBuilder: (context,index){
              return Padding(
                padding: const EdgeInsets.only(top: 4),
                child: Container(
                  height: 100,
                  width: 200,
                  child: Card(
                    elevation: 3,
                    color: AppColors.white,
                    child: Row(
                      children: [
                       Padding(
                         padding: const EdgeInsets.only(left: 10,right: 10),
                         child: Container(
                          height: 70,width: 70,
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(12),
                            image: DecorationImage(image: AssetImage('assets/images/ca.jpg',),fit: BoxFit.cover),
      
                          ),
                         ),
                       ),
      
                       Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        mainAxisAlignment: MainAxisAlignment.center,
                         children: [
                           InkWell(
                            onTap: (){},
                             child: Text('omar',style: TextStyle(
                              color: AppColors.primary,fontWeight: FontWeight.w700,fontSize: 18
                             ),),
                           ),
      
      Text('Sponsorships',style: TextStyle(
                            color: AppColors.primary.withOpacity(0.5),fontWeight: FontWeight.w500
                           ),),
      
      Text('monthly',style: TextStyle(
                            color: AppColors.primary.withOpacity(0.5),fontWeight: FontWeight.w500
                           ),),
      
      
      
      
                         ],
                       ),
      
      SizedBox(width: 40,),
                       Text('1200 '+'\$' ,style: TextStyle(
                            color: AppColors.secondary,fontWeight: FontWeight.w700
                           ),
                       ),
                  SizedBox(width: 40,),
                  
                  IconButton(onPressed: (){
                      ScaffoldMessenger.of(context).showSnackBar(
                      SnackBar(content: Text('has been deleted')),
                    );
                  }, icon: Icon(Icons.delete,color: AppColors.primary,))     
                      ],
      
                    ),
                  ),
                ),
              );
             }),
           )
           ,Container(
            height: 105,width: double.infinity,
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(20),
              color: AppColors.white
              
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Padding(
                  padding: const EdgeInsets.only(left: 15,right: 15,bottom: 10),
                  child: Text('Total ammount :  12\$',style: TextStyle(
                    color: AppColors.black,fontWeight: FontWeight.w600,fontSize: 18
                  ),),
                ),
                Center(
                  child: ElevatedButton(onPressed: (){}, child: Text('Pay Now'),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColors.secondary,
                    foregroundColor: AppColors.white,
                    fixedSize: Size(300, 30)
                  ),),
                )
              ],
            ),
           )
          ],
        ),
      ),
    );
  

    
  }
}