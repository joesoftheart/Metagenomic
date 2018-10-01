#Rscript file.R or R < scriptName.R --no-save/--save
#rm(list=ls())
args <- commandArgs(TRUE)
data=read.table(args[1], header=T, sep="\t")

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
svg(filename=args[2],width=8, height=6)
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

