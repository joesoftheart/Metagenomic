

#Rscript file.R or R < scriptName.R --no-save/--save
rm(list=ls())
data=read.table("outaverage.txt", header=T, sep="\t")

colnames(data)[1] <- "numsampled"
data1=data[order(data$numsampled),]

count = 0 
name_sample = c()
pch_16 = c()
lty_1 = c()

for (i in colnames(data)) {

    if(count > 0){
        name_sample <- c(name_sample,i)
        pch_16 <- c(pch_16 ,16)
        lty_1 <- c(lty_1,1)
    } 
    count = count+1   
}
svg(filename="Test.svg",width=8, height=6)
color_num = 1
color <- rainbow(length(name_sample))

for (i in name_sample) {

     if(color_num == 1){
         plot(x=data1$numsampled, y=data1[, i], type="l", col=color[color_num], xlab="# sequences sampled", ylab="Observed OTUs", ylim = c(0, max(data1[,names(data1) != "numsampled"])+50)) #, ylim=c(0,300))#, xlim=c(0,130000), ylim=c(0,900))
     }else{
        lines(x=data1$numsampled, y=data1[, i], type="l", col=color[color_num])
     }

     color_num = color_num+1
}

legend('topleft',name_sample, pch=pch_16,lty=lty_1, col=c(color, ncol=1), cex=0.8)
dev.off()









#tiff('Fig5_rarefactionpiglet.tiff',width=8, height=6, units="in", res=300, compression = "lzw")
# svg(filename="Test.svg",width=8, height=6)
#png('Fig5_rarefactionpiglet.png',width=8, height=6, units="in", res=300)
# plot(x=data1$numsampled, y=data1$T1R1, type="l", col="blue", xlab="# sequences sampled", ylab="Observed OTUs", ylim = c(0, max(data1[,names(data1) != "numsampled"])+10)) #, ylim=c(0,300))#, xlim=c(0,130000), ylim=c(0,900))
# lines(x=data1$numsampled, y=data1$T1R3, type="l", col="blue")
# lines(x=data1$numsampled, y=data1$T1R4, type="l",col="blue")
# lines(x=data1$numsampled, y=data1$T2R1, type="l",col="red")
# lines(x=data1$numsampled, y=data1$T2R3, type="l",col="red")
# lines(x=data1$numsampled, y=data1$T2R6, type="l",col="red")
# lines(x=data1$numsampled, y=data1$T3R2, type="l",col="green")
# lines(x=data1$numsampled, y=data1$T3R3, type="l",col="green")
# lines(x=data1$numsampled, y=data1$T3R6, type="l",col="green")
# lines(x=data1$numsampled, y=data1$T4R2, type="l",col="orange")
# legend('topleft',c('T1R1','T1R3','T1R4','T2R1','T2R3','T2R6','T3R2','T3R3','T3R6','T4R2'), pch=c(16,16,16,16,16,16,16,16,16,16),lty=c(1,1,1,1,1,1,1,1,1,1), col=c('blue','blue','blue','red','red','red','green','green','green','orange', ncol=1), cex=0.8)
# dev.off()

