#Rscript file.R or R < scriptName.R --no-save/--save
#rm(list=ls())
args <- commandArgs(TRUE)
library("reshape2")
library("ggplot2")

data=read.table(args[1], header=T, sep="\t")
#tiff('Fig5_rarefactionpiglet.tiff',width=8, height=6, units="in", res=300, compression = "lzw")
colnames(data)[1] <- "numsampled"
data1=data[order(data$numsampled),]

longRareData <- melt(data1, id.vars = "numsampled")
head(longRareData)

options(scipen = 999) #turn it off, back on use options(scipen=0)
p=ggplot(data = longRareData, 
         aes(x = numsampled, y = value, color = variable)) +
  geom_line(na.rm=TRUE) + 
  ggtitle("") +
  labs(x = "# Sequences sampled", y = "Observed OTUs") +
  guides(color = guide_legend("")) +
  theme_bw() + theme(legend.position="bottom", legend.text = element_text(size=12), text = element_text(size=18), axis.text.x = element_text(size=14), axis.text.y=element_text(size = 14))#, panel.border = element_blank(),panel.grid.major = element_blank(),panel.grid.minor = element_blank(), axis.line = element_line(colour = "black")) #
svg(filename=args[2],width=8, height=6)
#p
p+scale_color_manual(values=rainbow(length(colnames(data1)[-1])))
dev.off()




# #Rscript file.R or R < scriptName.R --no-save/--save
# #rm(list=ls())
# args <- commandArgs(TRUE)
# data=read.table(args[1], header=T, sep="\t")

# colnames(data)[1] <- "numsampled"
# data1=data[order(data$numsampled),]

# count = 0 
# name_sample = c()
# pch_16 = c()
# lty_1 = c()

# for (i in colnames(data)) {

#     if(count > 0){
#         name_sample <- c(name_sample,i)
#         pch_16 <- c(pch_16 ,16)
#         lty_1 <- c(lty_1,1)
#     } 
#     count = count+1   
# }
# svg(filename=args[2],width=8, height=6)
# color_num = 1
# color <- rainbow(length(name_sample))

# for (i in name_sample) {

#      if(color_num == 1){
#          plot(x=data1$numsampled, y=data1[, i], type="l", col=color[color_num], xlab="# sequences sampled", ylab="Observed OTUs", ylim = c(0, max(data1[,names(data1) != "numsampled"])+50)) #, ylim=c(0,300))#, xlim=c(0,130000), ylim=c(0,900))
#      }else{
#         lines(x=data1$numsampled, y=data1[, i], type="l", col=color[color_num])
#      }

#      color_num = color_num+1
# }

# legend('topleft',name_sample, pch=pch_16,lty=lty_1, col=c(color, ncol=1), cex=0.8)
# dev.off()

